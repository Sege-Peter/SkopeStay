<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? 'contact';

        // ── Get Availability ──────────────────────────────────────────────────
        if ($action === 'get_availability') {
            $service   = $data['service']   ?? 'Room';
            $check_in  = $data['check_in']  ?? date('Y-m-d');
            $check_out = $data['check_out'] ?? null;
            $guests    = (int)($data['guests'] ?? 1);

            $nights = 1;
            if ($check_in && $check_out) {
                $diff = (strtotime($check_out) - strtotime($check_in)) / 86400;
                if ($diff > 0) $nights = (int)$diff;
            }

            $options = [];
            if ($service === 'Room') {
                $stmt = $pdo->prepare("SELECT id, CONCAT(type, ' - Room ', room_number) as name, (price * ?) as total_price, features as description FROM rooms WHERE status='Available' AND max_occupancy >= ? LIMIT 10");
                $stmt->execute([$nights, $guests]);
                $options = $stmt->fetchAll();
            } elseif ($service === 'Hall') {
                $stmt = $pdo->prepare("SELECT id, name, (daily_rate * ?) as total_price, amenities as description FROM halls WHERE status='Available' LIMIT 10");
                $stmt->execute([$nights]);
                $options = $stmt->fetchAll();
            } elseif ($service === 'Restaurant') {
                $options[] = ['id' => 'restaurant_std', 'name' => 'Fine Dining Reservation', 'total_price' => 2500 * $guests, 'description' => 'Gourmet meal for ' . $guests . ' guests'];
            } elseif ($service === 'Pool') {
                $options[] = ['id' => 'pool_std', 'name' => 'Full Day Pool Pass', 'total_price' => 1000 * $guests, 'description' => 'Unlimited pool access for ' . $guests . ' guests'];
            }

            echo json_encode(['success' => true, 'options' => $options]);
            exit;
        }

        // ── Full Booking from Public Wizard ───────────────────────────────────
        if ($action === 'book') {
            $service    = $data['service']     ?? 'Room';
            $target_id  = $data['target_id']   ?? null;
            $check_in   = $data['check_in']    ?? date('Y-m-d');
            $check_out  = $data['check_out']   ?? null;
            $guests     = (int)($data['guests'] ?? 1);
            $first_name = trim($data['first_name'] ?? '');
            $last_name  = trim($data['last_name']  ?? '');
            $email      = trim($data['email']      ?? '');
            $phone      = trim($data['phone']      ?? '');
            $guest_name = trim("$first_name $last_name") ?: 'Guest';

            if (!$first_name || !$email || !$phone) {
                echo json_encode(['success' => false, 'message' => 'Please fill in all required guest details.']);
                exit;
            }

            $nights = 1;
            if ($check_in && $check_out) {
                $diff = (strtotime($check_out) - strtotime($check_in)) / 86400;
                if ($diff > 0) $nights = (int)$diff;
            }

            $room_id = null;
            $total   = (float)($data['total'] ?? 0);
            $status  = 'Pending';

            if ($service === 'Room') {
                if ($target_id) {
                    $room_id = $target_id;
                    $pdo->prepare("UPDATE rooms SET status='Occupied' WHERE id=?")->execute([$room_id]);
                    $status = 'Active';
                } else {
                    // Fallback
                    $avail = $pdo->query("SELECT id, price FROM rooms WHERE status='Available' ORDER BY RAND() LIMIT 1")->fetch();
                    if ($avail) {
                        $room_id = $avail['id'];
                        $total   = $avail['price'] * $nights;
                        $pdo->prepare("UPDATE rooms SET status='Occupied' WHERE id=?")->execute([$room_id]);
                        $status  = 'Active';
                    }
                }
            } elseif ($service === 'Hall') {
                if (!$total) $total = 15000 * $nights;
            } elseif ($service === 'Restaurant') {
                if (!$total) $total = 2500 * $guests;
            } elseif ($service === 'Pool') {
                if (!$total) $total = 1000 * $guests;
            }

            // Check which optional columns exist
            $cols      = $pdo->query("SHOW COLUMNS FROM bookings")->fetchAll(PDO::FETCH_COLUMN);
            $has_email = in_array('guest_email', $cols);
            $has_phone = in_array('guest_phone', $cols);
            $has_type  = in_array('booking_type', $cols);

            if ($has_email && $has_phone && $has_type) {
                $stmt = $pdo->prepare(
                    "INSERT INTO bookings (room_id, guest_name, guest_email, guest_phone, booking_type, check_in, check_out, total_amount, status)
                     VALUES (?,?,?,?,?,?,?,?,?)"
                );
                $stmt->execute([$room_id, $guest_name, $email, $phone, $service, $check_in, $check_out, $total, $status]);
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO bookings (room_id, guest_name, check_in, check_out, total_amount, status)
                     VALUES (?,?,?,?,?,?)"
                );
                $stmt->execute([$room_id, $guest_name, $check_in, $check_out, $total, $status]);
            }

            $booking_id = $pdo->lastInsertId();
            $ref = '#BK-' . str_pad($booking_id, 5, '0', STR_PAD_LEFT);

            echo json_encode([
                'success'    => true,
                'message'    => "Booking confirmed! Reference: $ref",
                'booking_id' => $booking_id,
                'ref'        => $ref,
                'total'      => $total,
                'nights'     => $nights,
            ]);
        }

        // ── Contact / Inquiry ─────────────────────────────────────────────────
        elseif ($action === 'contact') {
            $stmt = $pdo->prepare("INSERT INTO inquiries (full_name, email, phone, inquiry_type, message) VALUES (?,?,?,?,?)");
            $stmt->execute([
                $data['full_name']    ?? 'Guest',
                $data['email']        ?? null,
                $data['phone']        ?? null,
                $data['inquiry_type'] ?? 'Contact',
                $data['message']      ?? ''
            ]);
            echo json_encode(['success' => true, 'message' => 'Thank you! Your inquiry has been received.']);
        }

        // ── Legacy availability check ─────────────────────────────────────────
        elseif ($action === 'check_availability') {
            $msg = "Check-In: {$data['check_in']} | Check-Out: {$data['check_out']} | Guests: {$data['guests']} | Type: {$data['facility_type']}";
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
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
