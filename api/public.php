<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

// Fixed public rates for services that have no per-item price table
const RESTAURANT_RATE_PER_GUEST = 2500;
const POOL_RATE_PER_GUEST = 1000;

/** Which public services are switched on in Settings > Hospitality Rules. */
function service_enabled($pdo, $service) {
    $map = ['Room' => 'enable_online_booking', 'Hall' => 'enable_hall_booking', 'Restaurant' => 'enable_restaurant_pos', 'Pool' => 'enable_pool_passes'];
    return isset($map[$service]) && get_setting($pdo, $map[$service], '1') === '1';
}

/** Validated [check_in, check_out|null, nights] or an error string. */
function parse_stay($check_in, $check_out) {
    $in = DateTime::createFromFormat('Y-m-d', (string)$check_in);
    if (!$in) return 'Please choose a valid arrival date.';
    if ($in->format('Y-m-d') < date('Y-m-d')) return 'Arrival date cannot be in the past.';
    if (!$check_out) return [$in->format('Y-m-d'), null, 1];
    $out = DateTime::createFromFormat('Y-m-d', (string)$check_out);
    if (!$out || $out <= $in) return 'Departure must be after arrival.';
    $nights = (int)$in->diff($out)->days;
    if ($nights > 60) return 'Online bookings are limited to 60 nights. Please contact us for longer stays.';
    return [$in->format('Y-m-d'), $out->format('Y-m-d'), $nights];
}

/** Rooms free for the whole stay (no overlapping pending/active booking). */
function available_rooms($pdo, $check_in, $check_out, $guests, $room_id = null) {
    $end = $check_out ?: date('Y-m-d', strtotime($check_in . ' +1 day'));
    $sql = "SELECT id, room_number, type, price, features, max_occupancy FROM rooms r
            WHERE r.status IN ('Available', 'Cleaning') AND COALESCE(r.available_online, 1) = 1 AND r.max_occupancy >= ?
              AND NOT EXISTS (
                  SELECT 1 FROM bookings b WHERE b.room_id = r.id AND b.status IN ('Pending', 'Active')
                    AND DATE(b.check_in) < ? AND DATE(COALESCE(b.check_out, DATE_ADD(b.check_in, INTERVAL 1 DAY))) > ?
              )";
    $params = [$guests, $end, $check_in];
    if ($room_id) { $sql .= " AND r.id = ?"; $params[] = $room_id; }
    $st = $pdo->prepare($sql . " ORDER BY r.price ASC LIMIT 20");
    $st->execute($params);
    return $st->fetchAll();
}

try {
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true) ?: [];
        $action = $data['action'] ?? 'contact';

        // ── Get Availability ──────────────────────────────────────────────────
        if ($action === 'get_availability') {
            $service = $data['service'] ?? 'Room';
            $guests  = max(1, min(20, (int)($data['guests'] ?? 1)));
            if (!service_enabled($pdo, $service)) {
                echo json_encode(['success' => false, 'message' => 'Online booking for this service is currently unavailable. Please contact reception.']);
                exit;
            }
            $stay = parse_stay($data['check_in'] ?? date('Y-m-d'), $data['check_out'] ?? null);
            if (is_string($stay)) { echo json_encode(['success' => false, 'message' => $stay]); exit; }
            [$check_in, $check_out, $nights] = $stay;

            $options = [];
            if ($service === 'Room') {
                foreach (available_rooms($pdo, $check_in, $check_out, $guests) as $r) {
                    $options[] = ['id' => $r['id'], 'name' => $r['type'] . ' - Room ' . $r['room_number'], 'total_price' => $r['price'] * $nights, 'description' => $r['features']];
                }
            } elseif ($service === 'Hall') {
                $stmt = $pdo->prepare("SELECT id, name, (daily_rate * ?) as total_price, amenities as description FROM halls WHERE status='Available' LIMIT 10");
                $stmt->execute([$nights]);
                $options = $stmt->fetchAll();
            } elseif ($service === 'Restaurant') {
                $options[] = ['id' => 'restaurant_std', 'name' => 'Fine Dining Reservation', 'total_price' => RESTAURANT_RATE_PER_GUEST * $guests, 'description' => 'Gourmet meal for ' . $guests . ' guests'];
            } elseif ($service === 'Pool') {
                $options[] = ['id' => 'pool_std', 'name' => 'Full Day Pool Pass', 'total_price' => POOL_RATE_PER_GUEST * $guests, 'description' => 'Unlimited pool access for ' . $guests . ' guests'];
            }

            echo json_encode(['success' => true, 'options' => $options]);
            exit;
        }

        // ── Full Booking from Public Wizard ───────────────────────────────────
        if ($action === 'book') {
            $service    = $data['service']     ?? 'Room';
            $target_id  = $data['target_id']   ?? null;
            $guests     = max(1, min(20, (int)($data['guests'] ?? 1)));
            $first_name = trim(strip_tags($data['first_name'] ?? ''));
            $last_name  = trim(strip_tags($data['last_name']  ?? ''));
            $email      = trim($data['email'] ?? '');
            $phone      = trim(strip_tags($data['phone'] ?? ''));
            $guest_name = mb_substr(trim("$first_name $last_name"), 0, 100) ?: 'Guest';

            if (!service_enabled($pdo, $service)) {
                echo json_encode(['success' => false, 'message' => 'Online booking for this service is currently unavailable. Please contact reception.']);
                exit;
            }
            if (!$first_name || !$email || !$phone) {
                echo json_encode(['success' => false, 'message' => 'Please fill in all required guest details.']);
                exit;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
                exit;
            }
            $stay = parse_stay($data['check_in'] ?? date('Y-m-d'), $data['check_out'] ?? null);
            if (is_string($stay)) { echo json_encode(['success' => false, 'message' => $stay]); exit; }
            [$check_in, $check_out, $nights] = $stay;

            // Prices are always computed here; the client total is ignored
            $room_id = null;
            $total = 0;
            if ($service === 'Room') {
                $rooms = available_rooms($pdo, $check_in, $check_out, $guests, $target_id ?: null);
                if (!$rooms) {
                    echo json_encode(['success' => false, 'message' => $target_id ? 'Sorry, that room was just booked for those dates. Please pick another.' : 'No rooms are available for those dates.']);
                    exit;
                }
                $room_id = $rooms[0]['id'];
                $total = $rooms[0]['price'] * $nights;
            } elseif ($service === 'Hall') {
                $hall = $pdo->prepare("SELECT daily_rate FROM halls WHERE id = ? AND status = 'Available'");
                $hall->execute([$target_id]);
                $rate = $hall->fetchColumn();
                if ($rate === false) { echo json_encode(['success' => false, 'message' => 'Please choose an available venue.']); exit; }
                $total = $rate * $nights;
            } elseif ($service === 'Restaurant') {
                $total = RESTAURANT_RATE_PER_GUEST * $guests;
            } elseif ($service === 'Pool') {
                $total = POOL_RATE_PER_GUEST * $guests;
            } else {
                echo json_encode(['success' => false, 'message' => 'Unknown service.']);
                exit;
            }

            $stmt = $pdo->prepare(
                "INSERT INTO bookings (room_id, guest_name, guest_email, guest_phone, booking_type, check_in, check_out, total_amount, status)
                 VALUES (?,?,?,?,?,?,?,?, 'Pending')"
            );
            $stmt->execute([$room_id, $guest_name, $email, $phone, $service, $check_in, $check_out, $total]);

            $booking_id = $pdo->lastInsertId();
            $ref = '#' . get_setting($pdo, 'booking_prefix', 'BK') . '-' . str_pad($booking_id, 5, '0', STR_PAD_LEFT);
            log_action($pdo, null, 'public_booking', 'Bookings', "Online $service booking $ref by $guest_name", (int)$booking_id);

            echo json_encode([
                'success'    => true,
                'message'    => "Booking received! Reference: $ref. Our team will confirm shortly.",
                'booking_id' => $booking_id,
                'ref'        => $ref,
                'total'      => $total,
                'nights'     => $nights,
            ]);
        }

        // ── Newsletter ────────────────────────────────────────────────────────
        elseif ($action === 'subscribe') {
            $email = strtolower(trim($data['email'] ?? ''));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
                exit;
            }
            $pdo->prepare("INSERT IGNORE INTO newsletter_subscribers (email, source) VALUES (?, 'footer')")->execute([$email]);
            echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
        }

        // ── Contact / Inquiry ─────────────────────────────────────────────────
        elseif ($action === 'contact') {
            $name = mb_substr(trim(strip_tags($data['full_name'] ?? '')), 0, 150);
            $email = trim($data['email'] ?? '');
            $message = mb_substr(trim(strip_tags($data['message'] ?? '')), 0, 5000);
            if ($name === '' || $message === '') {
                echo json_encode(['success' => false, 'message' => 'Please include your name and a message.']);
                exit;
            }
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO inquiries (full_name, email, phone, inquiry_type, message) VALUES (?,?,?,?,?)");
            $stmt->execute([
                $name,
                $email ?: null,
                mb_substr(trim(strip_tags($data['phone'] ?? '')), 0, 50) ?: null,
                mb_substr(trim(strip_tags($data['inquiry_type'] ?? 'Contact')), 0, 100) ?: 'Contact',
                $message
            ]);
            echo json_encode(['success' => true, 'message' => 'Thank you! Your inquiry has been received.']);
        }

        // ── Legacy availability check ─────────────────────────────────────────
        elseif ($action === 'check_availability') {
            $msg = sprintf('Check-In: %s | Check-Out: %s | Guests: %s | Type: %s',
                strip_tags($data['check_in'] ?? ''), strip_tags($data['check_out'] ?? ''), (int)($data['guests'] ?? 1), strip_tags($data['facility_type'] ?? ''));
            $stmt = $pdo->prepare("INSERT INTO inquiries (full_name, inquiry_type, message) VALUES (?,?,?)");
            $stmt->execute(['Guest Booking Inquiry', 'Booking Request', $msg]);
            echo json_encode(['success' => true, 'message' => 'Your request has been sent! Our team will confirm availability shortly.']);
        }

        else {
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    }
} catch (Exception $e) {
    error_log('public api: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again or contact us directly.']);
}
