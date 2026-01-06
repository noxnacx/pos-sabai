<template>
  <div data-theme="emerald" class="min-h-screen bg-slate-100 pb-20">

    <div class="navbar bg-white shadow-sm sticky top-0 z-50 px-4 h-16">
      <div class="flex-1">
        <a class="btn btn-ghost normal-case text-xl text-slate-800 font-black gap-2">
          <span class="text-3xl">🤵</span> Waiter Station
        </a>
      </div>
      <div class="flex-none gap-2">
        <div class="badge badge-ghost text-xs hidden sm:flex text-slate-400 font-mono">Auto-Refresh: ON</div>

        <button @click="logout" class="btn btn-sm btn-ghost text-red-500 hover:bg-red-50" title="ออกจากระบบ">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 01-3-3h4a3 3 0 013 3v1" /></svg>
          <span class="hidden sm:inline">ออก</span>
        </button>
      </div>
    </div>

    <div class="p-4 max-w-7xl mx-auto">

      <div class="grid grid-cols-2 gap-4 mb-6">

        <button
          @click="activeTab = 'ready'"
          class="flex flex-col sm:flex-row items-center justify-center gap-2 p-4 rounded-2xl border-2 transition-all duration-200 shadow-sm h-24 sm:h-20"
          :class="activeTab === 'ready'
            ? 'bg-red-500 border-red-500 text-white shadow-red-200 shadow-lg scale-[1.02]'
            : 'bg-white border-slate-200 text-slate-400 hover:border-red-200 hover:text-red-400'"
        >
          <div class="relative">
            <span class="text-3xl">🔔</span>
            <span v-if="readyOrders.length > 0" class="absolute -top-2 -right-2 bg-white text-red-500 font-bold text-xs w-6 h-6 rounded-full flex items-center justify-center shadow-sm border border-red-100">
              {{ readyOrders.length }}
            </span>
          </div>
          <div class="flex flex-col items-center sm:items-start leading-tight">
            <span class="text-lg font-bold">อาหารเสร็จ</span>
            <span class="text-xs opacity-80 font-medium">รอเสิร์ฟ</span>
          </div>
        </button>

        <button
          @click="activeTab = 'served'"
          class="flex flex-col sm:flex-row items-center justify-center gap-2 p-4 rounded-2xl border-2 transition-all duration-200 shadow-sm h-24 sm:h-20"
          :class="activeTab === 'served'
            ? 'bg-emerald-500 border-emerald-500 text-white shadow-emerald-200 shadow-lg scale-[1.02]'
            : 'bg-white border-slate-200 text-slate-400 hover:border-emerald-200 hover:text-emerald-400'"
        >
          <div class="relative">
            <span class="text-3xl">💰</span>
            <span v-if="servedOrders.length > 0" class="absolute -top-2 -right-2 bg-white text-emerald-500 font-bold text-xs w-6 h-6 rounded-full flex items-center justify-center shadow-sm border border-emerald-100">
              {{ servedOrders.length }}
            </span>
          </div>
          <div class="flex flex-col items-center sm:items-start leading-tight">
            <span class="text-lg font-bold">เช็คบิล</span>
            <span class="text-xs opacity-80 font-medium">รอเก็บเงิน</span>
          </div>
        </button>

      </div>

      <div v-if="activeTab === 'ready'">
        <div v-if="readyOrders.length === 0" class="flex flex-col items-center justify-center py-20 opacity-50">
          <div class="text-8xl mb-4 grayscale">🍽️</div>
          <div class="text-xl font-bold text-slate-400">ยังไม่มีอาหารเสร็จ</div>
          <p class="text-slate-400">ว่างจัง... พักดื่มน้ำก่อนได้นะ</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="order in readyOrders" :key="order.id" class="card bg-white shadow-lg border-l-8 border-red-500 rounded-xl overflow-hidden hover:shadow-xl transition-shadow">
            <div class="card-body p-5">
              <div class="flex justify-between items-start mb-2">
                <div>
                   <h2 class="text-2xl font-black text-slate-800 flex items-center gap-2">
                     โต๊ะ {{ order.table?.name }}
                   </h2>
                   <p class="text-xs text-slate-400 font-mono">#{{ order.invoice_number }}</p>
                </div>
                <div class="badge badge-error text-white font-bold animate-pulse shadow-md">ด่วน</div>
              </div>

              <div class="divider my-1"></div>

              <ul class="space-y-3 my-2">
                <li v-for="item in order.items" :key="item.id" class="flex justify-between items-center bg-red-50 p-3 rounded-lg border border-red-100">
                  <span class="text-slate-700 font-bold">{{ item.product_name }}</span>
                  <span class="text-xl font-black text-red-500">x{{ item.quantity }}</span>
                </li>
              </ul>

              <div class="card-actions mt-4">
                <button
                  @click="markServed(order.id)"
                  class="btn bg-slate-200 hover:bg-slate-300 text-slate-700 border-none w-full text-lg shadow-sm"
                >
                  🏃‍♂️ นำไปเสิร์ฟ
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'served'">
        <div v-if="servedOrders.length === 0" class="flex flex-col items-center justify-center py-20 opacity-50">
          <div class="text-8xl mb-4 grayscale">🍃</div>
          <div class="text-xl font-bold text-slate-400">ไม่มีรายการรอเช็คบิล</div>
          <p class="text-slate-400">ร้านโล่งจัง...</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="order in servedOrders" :key="order.id" class="card bg-white shadow-lg border border-slate-200 rounded-xl overflow-hidden hover:shadow-xl transition-shadow">
            <div class="bg-emerald-50 px-5 py-3 border-b border-emerald-100 flex justify-between items-center">
               <h2 class="text-xl font-bold text-emerald-800">โต๊ะ {{ order.table?.name }}</h2>
               <div class="badge bg-white text-emerald-600 border-none font-bold shadow-sm">กำลังทาน</div>
            </div>

            <div class="card-body p-5 text-center">
               <div class="py-4">
                  <span class="text-sm text-slate-400 uppercase tracking-wide">ยอดสุทธิ</span>
                  <div class="text-5xl font-black text-emerald-600 mt-1">฿{{ Number(order.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</div>
                  <div class="text-sm text-slate-400 mt-2 font-medium bg-slate-100 inline-block px-3 py-1 rounded-full">{{ order.items.length }} รายการ</div>
               </div>

               <div class="flex flex-col gap-3 mt-2">
                 <button @click="generateQR(order)" class="btn bg-blue-600 hover:bg-blue-700 text-white w-full text-lg shadow-md shadow-blue-100 border-none h-12">
                   📲 สแกนจ่าย (PromptPay)
                 </button>

                 <button @click="checkout(order)" class="btn bg-emerald-600 hover:bg-emerald-700 text-white w-full text-lg shadow-md shadow-emerald-100 border-none h-12">
                   💵 รับเงินสด / ปิดโต๊ะ
                 </button>
               </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <dialog id="qr_modal" class="modal backdrop-blur-sm">
      <div class="modal-box text-center bg-white border-t-8 border-blue-500 shadow-2xl">
        <h3 class="font-bold text-2xl mb-2 text-slate-800">📲 สแกนจ่ายเงิน</h3>
        <p class="text-slate-500 mb-6">โต๊ะ: <span class="font-bold text-blue-600 text-lg">{{ currentQRTable }}</span></p>

        <div class="bg-white p-4 inline-block rounded-2xl shadow-[0_0_20px_rgba(0,0,0,0.1)] border border-slate-100 mb-6">
           <img :src="qrCodeUrl" alt="QR Code" class="w-64 h-64 mx-auto object-contain bg-white">
        </div>

        <div class="text-4xl font-black text-blue-600 mb-2">฿{{ Number(currentQRTotal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</div>
        <p class="text-xs text-red-400 mb-8">*กรุณาตรวจสอบสลิปเงินเข้าก่อนกดปิดโต๊ะ</p>

        <div class="flex gap-3">
          <form method="dialog" class="flex-1">
            <button class="btn btn-outline border-slate-300 text-slate-500 hover:bg-slate-100 hover:text-slate-700 w-full">ยกเลิก</button>
          </form>
          <button @click="confirmQRPayment" class="btn bg-blue-600 hover:bg-blue-700 text-white border-none flex-1 shadow-lg shadow-blue-200">
             ✅ เงินเข้าแล้ว
          </button>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-slate-900/50"><button>close</button></form>
    </dialog>

    <dialog id="notification_modal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box bg-white border-t-8 border-emerald-500 shadow-2xl p-8">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-50 mb-6 animate-bounce">
            <span class="text-4xl">🛎️</span>
          </div>
          <h3 class="text-2xl font-black text-slate-800 mb-2">อาหารเสร็จแล้ว!</h3>
          <p class="text-lg text-slate-600 mb-6">
            โต๊ะ <span class="text-4xl font-black text-emerald-600 block mt-2">{{ latestReadyOrder?.table?.name }}</span>
          </p>
          <p class="text-sm text-slate-400 mb-8">กรุณานำอาหารไปเสิร์ฟให้ลูกค้า</p>
        </div>
        <div class="modal-action justify-center w-full">
          <form method="dialog" class="w-full">
            <button class="btn bg-emerald-600 hover:bg-emerald-700 text-white w-full text-xl h-14 border-none shadow-lg shadow-emerald-200 rounded-xl">
              รับทราบ 👌
            </button>
          </form>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-slate-900/60"><button>close</button></form>
    </dialog>

    <div id="receipt-area">
      <div v-if="printingOrder" class="receipt-content">
        <div class="text-center">
          <h2 class="bold" style="font-size: 18px; margin: 0;">ร้านอาหาร POS Sabai</h2>
          <p style="margin: 5px 0;">ใบเสร็จรับเงิน (Receipt)</p>
        </div>
        <div class="line"></div>
        <div>
          <p style="margin: 0;">โต๊ะ: {{ printingOrder.table?.name }}</p>
          <p style="margin: 0;">เลขที่: {{ printingOrder.invoice_number }}</p>
          <p style="margin: 0;">วันที่: {{ new Date().toLocaleString('th-TH') }}</p>
        </div>
        <div class="line"></div>

        <table style="width: 100%; border-collapse: collapse;">
          <tr v-for="item in printingOrder.items" :key="item.id">
            <td style="vertical-align: top; padding-bottom: 5px;">
                <span style="font-weight: bold;">{{ item.product_name }}</span>
                <br>
                <span style="font-size: 10px; color: #555; padding-left: 5px;">
                    {{ item.quantity }} x {{ Number(item.subtotal / item.quantity).toFixed(2) }}
                </span>
            </td>
            <td class="text-right" style="vertical-align: top;">
                {{ Number(item.subtotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}
            </td>
          </tr>
        </table>

        <div class="line"></div>
        <div class="text-right">
          <p class="bold" style="font-size: 18px; margin: 5px 0;">
            ยอดรวม: {{ Number(printingOrder.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}
          </p>
        </div>
        <div class="line"></div>
        <div class="text-center" style="margin-top: 10px;">
          <p style="margin: 0;">ขอบคุณที่ใช้บริการ</p>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue';
import axios from 'axios';

// ✏️ แก้ไขเลข PromptPay
const shopPromptPayId = "0924120111";

const orders = ref([]);
const activeTab = ref('ready');
const printingOrder = ref(null);
const currentQRTotal = ref(0);
const currentQRTable = ref('');
const currentGroupedOrder = ref(null);
const qrCodeUrl = ref('');
let intervalId = null;

// --- Notification ---
const latestReadyOrder = ref(null);
const alertedOrderIds = ref(new Set());

const playNotificationSound = () => {
  const audio = new Audio('https://freesound.org/data/previews/320/320655_5260872-lq.mp3');
  audio.volume = 0.5;
  audio.play().catch(e => console.log('Audio blocked'));
}

const fetchOrders = async () => {
    try {
        const response = await axios.get('/api/waiter/orders');
        orders.value = response.data;
        checkNewReadyOrders();
    } catch (e) { console.error(e); }
}

const checkNewReadyOrders = () => {
    const readyItems = orders.value.filter(o => o.status === 'ready');
    readyItems.forEach(order => {
        if (!alertedOrderIds.value.has(order.id)) {
            alertedOrderIds.value.add(order.id);
            latestReadyOrder.value = order;
            const modal = document.getElementById('notification_modal');
            if (modal) {
                modal.showModal();
                playNotificationSound();
            }
        }
    });
}

const readyOrders = computed(() => orders.value.filter(o => o.status === 'ready'));

// 🟢 Served Logic (Merged Bills) - ✨ แก้ไขการบวกเลขทศนิยมแล้ว
const servedOrders = computed(() => {
    const rawServed = orders.value.filter(o => o.status === 'served');
    const groupedMap = new Map();

    rawServed.forEach(order => {
        const tableId = order.table_id;
        if (!groupedMap.has(tableId)) {
            groupedMap.set(tableId, {
                id: 'merged_' + tableId,
                table: order.table,
                table_id: tableId,
                invoice_number: order.invoice_number,
                created_at: order.created_at,
                total_amount: 0,
                items: [],
                original_order_ids: []
            });
        }
        const group = groupedMap.get(tableId);

        // ⚠️ ใช้ parseFloat เพื่อกัน String Concatenation
        group.total_amount += parseFloat(order.total_amount);
        group.original_order_ids.push(order.id);

        order.items.forEach(item => {
            const existingItem = group.items.find(i => i.product_id === item.product_id);
            // ⚠️ ใช้ parseFloat กับ subtotal ด้วย
            const itemSubtotal = parseFloat(item.subtotal || 0);

            if (existingItem) {
                existingItem.quantity += item.quantity;
                existingItem.subtotal += itemSubtotal;
            } else {
                // สร้าง object ใหม่ เพื่อไม่ให้ค่าทับกับ pointer เดิม และ set subtotal เป็น number
                group.items.push({ ...item, subtotal: itemSubtotal });
            }
        });
    });
    return Array.from(groupedMap.values());
});

const markServed = async (orderId) => {
    try {
        await axios.post(`/api/orders/${orderId}/status`, { status: 'served' });
        fetchOrders();
    } catch (e) { alert('Error'); }
}

const generateQR = (groupedOrder) => {
    currentQRTotal.value = groupedOrder.total_amount;
    currentQRTable.value = groupedOrder.table?.name;
    currentGroupedOrder.value = groupedOrder;
    qrCodeUrl.value = `https://promptpay.io/${shopPromptPayId}/${groupedOrder.total_amount}.png`;
    document.getElementById('qr_modal').showModal();
}

const confirmQRPayment = async () => {
    document.getElementById('qr_modal').close();
    await checkout(currentGroupedOrder.value);
}

const printReceipt = async (groupedOrder) => {
    printingOrder.value = groupedOrder;
    await nextTick();
    window.print();
}

const checkout = async (groupedOrder) => {
    if(!document.getElementById('qr_modal').open) {
       if(!confirm(`ยืนยันปิดโต๊ะ ${groupedOrder.table?.name} ยอดรวม ฿${groupedOrder.total_amount.toLocaleString()}?`)) return;
    }
    try {
        await printReceipt(groupedOrder);
        const promises = groupedOrder.original_order_ids.map(id =>
            axios.post(`/api/orders/${id}/checkout`)
        );
        await Promise.all(promises);
        fetchOrders();
    } catch (e) {
        console.error(e);
        alert('เกิดข้อผิดพลาดในการปิดบิล');
    }
}

const logout = async () => {
  if(!confirm('⚠️ คุณแน่ใจหรือไม่ว่าจะออกจากระบบ?')) return;
  try {
    await axios.post('/logout');
    window.location.href = '/login';
  } catch (e) { alert('Logout failed'); }
}

onMounted(() => {
    fetchOrders();
    intervalId = setInterval(fetchOrders, 5000);
});

onUnmounted(() => clearInterval(intervalId));
</script>

<style>
#receipt-area { display: none; }
@media print {
    body * { visibility: hidden; }
    #receipt-area, #receipt-area * { visibility: visible; display: block; }
    #receipt-area { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
    .receipt-content { font-family: monospace; font-size: 12px; color: black; width: 100%; padding: 10px; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .bold { font-weight: bold; }
    .line { border-bottom: 1px dashed black; margin: 5px 0; }
}
</style>
