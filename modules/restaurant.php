<?php
$page_title = "Restaurant POS | SkopeStay";
require_once '../includes/config.php';

// Get today's orders stats
$today_orders   = $pdo->query("SELECT COUNT(*) FROM restaurant_orders WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$today_revenue  = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM restaurant_orders WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM restaurant_orders WHERE status='Pending'")->fetchColumn();

// Fetch occupied rooms for Room Service
$occupied_rooms = $pdo->query("SELECT id, room_number FROM rooms WHERE status = 'Occupied'")->fetchAll();

// Fetch tables
$tables = $pdo->query("SELECT table_number, status FROM restaurant_tables")->fetchAll();

try {
    $menu = $pdo->query("SELECT name, price, category as cat, description as 'desc', badge, emoji, image_url FROM restaurant_items WHERE status = 'Available'")->fetchAll();
} catch (Exception $e) {
    $menu = [];
}

include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-gray-50 text-on-surface font-body-md min-h-screen">
<?php include '../includes/sidebar.php'; ?>

<div class="md:ml-64 flex flex-col h-screen overflow-hidden">
<?php include '../includes/header.php'; ?>

<!-- POS SPLIT LAYOUT -->
<div class="flex-1 flex overflow-hidden">

  <!-- LEFT: Menu -->
  <section class="flex-1 overflow-y-auto p-5 space-y-5">

    <!-- Breadcrumb + Stats -->
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <nav class="flex items-center gap-2 text-xs font-semibold text-on-surface-variant mb-1">
          <span>Modules</span><span class="material-symbols-outlined text-[14px]">chevron_right</span>
          <span class="text-primary font-bold">Restaurant POS</span>
        </nav>
        <h1 class="text-xl font-extrabold text-on-surface">Restaurant Menu</h1>
      </div>
      <div class="flex items-center gap-3">
        <?php foreach ([
          ['Today Orders', $today_orders,  'receipt_long', 'bg-primary/10 text-primary'],
          ['Today Revenue','KSh '.number_format((float)$today_revenue), 'payments', 'bg-green-100 text-green-700'],
          ['Pending',      $pending_orders,'pending_actions','bg-amber-100 text-amber-700'],
        ] as [$lbl,$val,$ico,$cls]): ?>
        <div class="flex items-center gap-2 bg-white border border-outline-variant/30 rounded-xl px-3 py-2 shadow-sm">
          <div class="w-8 h-8 rounded-lg <?= $cls ?> flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-[16px]"><?= $ico ?></span>
          </div>
          <div><p class="text-[10px] text-on-surface-variant font-semibold"><?= $lbl ?></p>
          <p class="font-extrabold text-sm text-on-surface"><?= $val ?></p></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex gap-2 overflow-x-auto pb-1">
      <?php foreach (['All','Breakfast','Lunch','Dinner','Drinks','Desserts'] as $c): ?>
      <button onclick="filterCat('<?= $c ?>')" data-cat="<?= $c ?>"
        class="cat-btn px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap border transition-all <?= $c==='All' ? 'bg-primary text-white border-primary shadow' : 'bg-white text-on-surface-variant border-outline-variant hover:border-primary hover:text-primary' ?>">
        <?= $c ?>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- Menu Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4" id="menu-grid">
      <?php foreach ($menu as $item): ?>
      <div class="menu-card bg-white rounded-[1.5rem] border border-gray-100 shadow-md overflow-hidden group cursor-pointer"
           style="transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease;"
           onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 20px 40px -12px rgba(0,0,0,0.18)'"
           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow=''"
           data-cat="<?= $item['cat'] ?>">

        <!-- Food Image -->
        <div class="h-44 relative overflow-hidden bg-gray-100">
          <?php if(!empty($item['image_url'])): ?>
            <img src="<?= htmlspecialchars($item['image_url']) ?>"
                 alt="<?= htmlspecialchars($item['name']) ?>"
                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                 loading="lazy"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
            <div class="w-full h-full hidden items-center justify-center absolute inset-0" style="background:linear-gradient(135deg,#f0f4ff,#e8f0fe)">
              <span class="text-6xl"><?= htmlspecialchars($item['emoji'] ?? '🍽️') ?></span>
            </div>
          <?php else: ?>
            <div class="w-full h-full flex items-center justify-center" style="background: linear-gradient(135deg,#f0f4ff 0%,#e8f0fe 100%)">
              <span class="text-6xl select-none group-hover:scale-110 transition-transform duration-300"><?= htmlspecialchars($item['emoji'] ?? '🍽️') ?></span>
            </div>
          <?php endif; ?>

          <!-- Badge -->
          <?php if(!empty($item['badge'])): ?>
          <div class="absolute top-2.5 right-2.5">
            <span class="bg-gray-900 text-white text-[9px] font-extrabold px-2.5 py-1 rounded-full tracking-widest uppercase"><?= htmlspecialchars($item['badge']) ?></span>
          </div>
          <?php endif; ?>

          <!-- Category pill -->
          <div class="absolute bottom-2.5 left-2.5">
            <span class="bg-white/80 backdrop-blur-sm text-gray-600 text-[10px] font-semibold px-2.5 py-0.5 rounded-full"><?= htmlspecialchars($item['cat']) ?></span>
          </div>

          <!-- Hover Add Button (floating) -->
          <button onclick="addToOrder('<?= addslashes($item['name']) ?>', <?= $item['price'] ?>)"
            class="absolute bottom-3 right-3 w-11 h-11 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-gray-800 shadow-lg
                   hover:bg-amber-400 hover:text-white transition-all duration-300 
                   translate-y-10 opacity-0 group-hover:translate-y-0 group-hover:opacity-100">
            <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
          </button>
        </div>

        <!-- Card Body -->
        <div class="px-4 py-3">
          <h3 class="font-bold text-sm text-gray-900 truncate mb-1" title="<?= htmlspecialchars($item['name']) ?>"><?= htmlspecialchars($item['name']) ?></h3>
          <p class="text-[11px] text-gray-400 mb-3 leading-relaxed line-clamp-2"><?= htmlspecialchars($item['desc'] ?? 'Gourmet selection') ?></p>
          <div class="flex justify-between items-center">
            <span class="font-extrabold text-amber-500 text-base">KSh <?= number_format($item['price']) ?></span>
            <button onclick="addToOrder('<?= addslashes($item['name']) ?>', <?= $item['price'] ?>)"
              class="py-1.5 px-3 bg-gray-900 text-white rounded-xl text-[11px] font-bold hover:bg-amber-500 active:scale-95 transition-all flex items-center gap-1 shadow-sm">
              <span class="material-symbols-outlined text-[14px]">add</span> Add
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- RIGHT: Order Cart -->
  <aside class="w-80 lg:w-96 bg-white border-l border-outline-variant/40 flex flex-col shadow-xl flex-shrink-0">

    <!-- Cart Header -->
    <div class="p-5 border-b border-outline-variant/30 flex-shrink-0">
      <div class="flex justify-between items-center mb-4">
        <h2 class="font-extrabold text-lg text-on-surface">Current Order</h2>
        <span id="order-badge" class="bg-primary/10 text-primary text-xs font-extrabold px-2.5 py-1 rounded-full">#<span id="order-num">4582</span></span>
      </div>
      <div class="space-y-3">
        <!-- Order Type -->
        <div>
          <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wide block mb-1">Order Type</label>
          <div class="flex items-center gap-2 px-3 py-2 bg-surface-container-low rounded-xl border border-outline-variant">
            <span class="material-symbols-outlined text-[16px] text-on-surface-variant">shopping_bag</span>
            <select id="order-type" onchange="toggleOrderType()" class="bg-transparent text-sm font-bold outline-none flex-1">
              <option value="Dine-In">Dine-In</option>
              <option value="Room Service">Room Service</option>
              <option value="Takeaway">Takeaway</option>
              <option value="Delivery">Delivery</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div id="table-container">
            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wide block mb-1">Table</label>
            <div class="flex items-center gap-2 px-3 py-2 bg-surface-container-low rounded-xl border border-outline-variant">
              <span class="material-symbols-outlined text-[16px] text-on-surface-variant">table_restaurant</span>
              <select id="table-num" class="bg-transparent text-sm font-bold outline-none flex-1">
                <option value="">Select Table</option>
                <?php foreach($tables as $tbl): ?><option value="<?= $tbl['table_number'] ?>"><?= $tbl['table_number'] ?> (<?= $tbl['status'] ?>)</option><?php endforeach; ?>
                <?php if(empty($tables)): ?>
                    <?php for($t=1;$t<=20;$t++): ?><option value="Table <?= $t ?>">Table <?= $t ?></option><?php endfor; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>
          <div id="room-container" style="display:none;">
            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wide block mb-1">Room</label>
            <div class="flex items-center gap-2 px-3 py-2 bg-surface-container-low rounded-xl border border-outline-variant">
              <span class="material-symbols-outlined text-[16px] text-on-surface-variant">bed</span>
              <select id="room-id" class="bg-transparent text-sm font-bold outline-none flex-1">
                <option value="">Select Room</option>
                <?php foreach($occupied_rooms as $rm): ?><option value="<?= $rm['id'] ?>">Room <?= $rm['room_number'] ?></option><?php endforeach; ?>
              </select>
            </div>
          </div>
          <div>
            <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wide block mb-1">Guest / Contact</label>
            <div class="flex items-center gap-2 px-3 py-2 bg-surface-container-low rounded-xl border border-outline-variant">
              <span class="material-symbols-outlined text-[16px] text-on-surface-variant">person</span>
              <input id="guest-name" type="text" placeholder="Walk-in Guest" class="bg-transparent text-sm outline-none flex-1 min-w-0"/>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Cart Items -->
    <div class="flex-1 overflow-y-auto p-4 space-y-2" id="order-items">
      <div class="flex flex-col items-center justify-center h-full py-10 text-on-surface-variant/40">
        <span class="material-symbols-outlined text-5xl mb-2">shopping_basket</span>
        <p class="text-xs font-semibold">Order is empty</p>
        <p class="text-[10px] mt-1">Add items from the menu</p>
      </div>
    </div>

    <!-- Totals + Actions -->
    <div class="p-5 border-t border-outline-variant/30 space-y-4 flex-shrink-0">
      <div class="space-y-2">
        <div class="flex justify-between text-sm"><span class="text-on-surface-variant">Subtotal</span><span class="font-bold" id="subtotal">KSh 0</span></div>
        <div class="flex justify-between text-sm"><span class="text-on-surface-variant">Tax (16%)</span><span class="font-bold" id="tax">KSh 0</span></div>
        <div class="flex justify-between pt-2 border-t border-outline-variant/30">
          <span class="font-extrabold text-on-surface">Grand Total</span>
          <span class="font-extrabold text-primary text-lg" id="total">KSh 0</span>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <button onclick="clearOrder()" class="py-2.5 bg-surface-container-high text-on-surface-variant rounded-xl text-xs font-bold hover:bg-red-50 hover:text-red-600 transition-colors flex items-center justify-center gap-1.5">
          <span class="material-symbols-outlined text-[16px]">delete_sweep</span> Clear
        </button>
        <button onclick="sendToKitchen()" class="py-2.5 bg-amber-100 text-amber-700 border border-amber-200 rounded-xl text-xs font-bold hover:bg-amber-200 transition-colors flex items-center justify-center gap-1.5">
          <span class="material-symbols-outlined text-[16px]">soup_kitchen</span> Kitchen
        </button>
      </div>
      <button onclick="checkout()" class="w-full py-3.5 rounded-xl font-extrabold text-sm text-white shadow-lg active:scale-[0.97] transition-all flex items-center justify-center gap-2"
              style="background:linear-gradient(135deg,rgb(37,99,235) 0%,rgb(29,78,216) 100%)">
        <span class="material-symbols-outlined">payments</span> Checkout &amp; Pay
      </button>
    </div>
  </aside>
</div>

<?php include '../includes/footer.php'; ?>
</div><!-- /md:ml-64 -->

<script>
let cart = [];

function fmt(v){ return 'KSh '+Math.round(v).toLocaleString(); }

function addToOrder(name, price){
    const ex = cart.find(i=>i.name===name);
    if(ex) ex.qty++;
    else cart.push({name, price, qty:1});
    render();
    // small bounce feedback
    Swal.fire({icon:'success',title:'Added!',text:name+' added to order.',timer:900,timerProgressBar:true,showConfirmButton:false,toast:true,position:'bottom-end'});
}

function remove(name){
    const i=cart.findIndex(x=>x.name===name);
    if(i>-1){ if(cart[i].qty>1) cart[i].qty--; else cart.splice(i,1); }
    render();
}

function clearOrder(){
    if(cart.length===0) return;
    Swal.fire({title:'Clear order?',icon:'warning',showCancelButton:true,confirmButtonColor:'rgb(239,68,68)',confirmButtonText:'Clear'})
    .then(r=>{ if(r.isConfirmed){cart=[];render();} });
}

function render(){
    const el=document.getElementById('order-items');
    if(cart.length===0){
        el.innerHTML=`<div class="flex flex-col items-center justify-center h-full py-10 text-on-surface-variant/40">
            <span class="material-symbols-outlined text-5xl mb-2">shopping_basket</span>
            <p class="text-xs font-semibold">Order is empty</p></div>`;
        document.getElementById('subtotal').textContent='KSh 0';
        document.getElementById('tax').textContent='KSh 0';
        document.getElementById('total').textContent='KSh 0';
        return;
    }
    el.innerHTML=cart.map(item=>`
        <div class="flex items-center gap-3 bg-surface-container-low p-3 rounded-xl border border-outline-variant/30 group">
            <div class="flex-1 min-w-0">
                <p class="font-bold text-xs text-on-surface truncate">${item.name}</p>
                <p class="text-[10px] text-on-surface-variant">${fmt(item.price)} each</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button onclick="remove('${item.name}')" class="w-6 h-6 flex items-center justify-center rounded-lg bg-white border border-outline-variant hover:bg-red-50 hover:text-red-500 transition-colors">
                    <span class="material-symbols-outlined text-[14px]">remove</span>
                </button>
                <span class="font-extrabold text-sm w-5 text-center">${item.qty}</span>
                <button onclick="addToOrder('${item.name}',${item.price})" class="w-6 h-6 flex items-center justify-center rounded-lg bg-white border border-outline-variant hover:bg-green-50 hover:text-green-600 transition-colors">
                    <span class="material-symbols-outlined text-[14px]">add</span>
                </button>
            </div>
            <p class="font-extrabold text-sm text-primary min-w-[60px] text-right">${fmt(item.price*item.qty)}</p>
        </div>`).join('');

    const sub=cart.reduce((a,i)=>a+(i.price*i.qty),0);
    const tax=sub*0.16;
    document.getElementById('subtotal').textContent=fmt(sub);
    document.getElementById('tax').textContent=fmt(tax);
    document.getElementById('total').textContent=fmt(sub+tax);
}

function sendToKitchen(){
    if(cart.length===0){ Swal.fire({icon:'warning',title:'Empty Order',text:'Add items before sending to kitchen.',timer:2000,toast:true,position:'top-end',showConfirmButton:false}); return; }
    Swal.fire({icon:'success',title:'Sent to Kitchen! 🍳',text:'Order #'+document.getElementById('order-num').textContent+' is being prepared.',timer:3000,timerProgressBar:true,showConfirmButton:false,toast:true,position:'top-end'});
}

async function checkout(){
    if(cart.length===0){ Swal.fire({icon:'warning',title:'Empty Order',text:'Add items first.',timer:2000,toast:true,position:'top-end',showConfirmButton:false}); return; }
    
    const type = document.getElementById('order-type').value;
    const table = document.getElementById('table-num').value;
    const roomId = document.getElementById('room-id').value;
    const guest = document.getElementById('guest-name').value || 'Walk-in Guest';
    
    if (type === 'Room Service' && !roomId) {
        Swal.fire({icon:'warning',title:'Room Required',text:'Please select a room for Room Service.',timer:2000,toast:true,position:'top-end',showConfirmButton:false});
        return;
    }
    
    if (type === 'Dine-In' && !table) {
        Swal.fire({icon:'warning',title:'Table Required',text:'Please select a table for Dine-In orders.',timer:2000,toast:true,position:'top-end',showConfirmButton:false});
        return;
    }

    const sub = cart.reduce((a,i)=>a+(i.price*i.qty),0);
    const total = parseFloat((sub * 1.16).toFixed(2));

    // Build the order payload to pass into the payment modal
    const orderPayload = {
        order_type: type,
        table_number: type === 'Dine-In' ? table : null,
        room_id: type === 'Room Service' ? roomId : null,
        guest_name: guest,
        items: cart,
        total: total
    };

    // Open the premium payment modal
    openPaymentModal(orderPayload, total);
}

function toggleOrderType() {
    const type = document.getElementById('order-type').value;
    document.getElementById('table-container').style.display = (type === 'Dine-In') ? 'block' : 'none';
    document.getElementById('room-container').style.display = (type === 'Room Service') ? 'block' : 'none';
}

function filterCat(cat){
    document.querySelectorAll('.cat-btn').forEach(b=>{
        const a=b.dataset.cat===cat;
        b.className=b.className.replace(/bg-primary text-white border-primary shadow|bg-white text-on-surface-variant border-outline-variant hover:border-primary hover:text-primary/g,'').trim();
        b.classList.add(...(a?['bg-primary','text-white','border-primary','shadow']:['bg-white','text-on-surface-variant','border-outline-variant','hover:border-primary','hover:text-primary']));
    });
    document.querySelectorAll('.menu-card').forEach(c=>{
        c.style.display=(cat==='All'||c.dataset.cat===cat)?'':'none';
    });
}
</script>

<?php include '../includes/payment_modal.php'; ?>

<script>
// Bridge: called by payment_modal when it's done so we can clear the cart
function finalizeCheckoutSuccess() {
    cart = [];
    render();
    document.getElementById('order-num').textContent = Math.floor(Math.random()*9000+1000);
}
</script>
</body>
</html>
