<template>
  <div data-theme="emerald" class="min-h-screen bg-base-200 pb-20">

    <div class="navbar bg-base-100 shadow-md sticky top-0 z-50">
      <div class="flex-1">
        <a class="btn btn-ghost normal-case text-xl text-primary font-bold">
          🤵 Waiter Station
        </a>
      </div>
      <div class="flex-none gap-2">
        <div class="badge badge-outline text-xs hidden sm:flex">Refresh: 5s</div>

        <button @click="logout" class="btn btn-ghost btn-sm text-error hover:bg-error hover:text-white" title="ออกจากระบบ">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 01-3-3h4a3 3 0 013 3v1" /></svg>
          <span class="hidden sm:inline">ออก</span>
        </button>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4 p-4">
      <div class="stats shadow bg-white">
        <div class="stat p-2 sm:p-4">
          <div class="stat-figure text-secondary hidden sm:block">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <div class="stat-title text-xs sm:text-sm">รอเสิร์ฟ</div>
          <div class="stat-value text-error text-2xl sm:text-4xl">{{ readyOrders.length }}</div>
          <div class="stat-desc">รายการ</div>
        </div>
      </div>

      <div class="stats shadow bg-white">
        <div class="stat p-2 sm:p-4">
          <div class="stat-figure text-secondary hidden sm:block">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
          </div>
          <div class="stat-title text-xs sm:text-sm">กำลังทาน</div>
          <div class="stat-value text-success text-2xl sm:text-4xl">{{ servedOrders.length }}</div>
          <div class="stat-desc">โต๊ะ</div>
        </div>
      </div>
    </div>

    <div class="p-4 pt-0">
      <div role="tablist" class="tabs tabs-boxed mb-4 bg-white">
        <a role="tab" :class="['tab', activeTab === 'ready' ? 'tab-active bg-error text-white' : '']" @click="activeTab = 'ready'">
          🔔 อาหารเสร็จ ({{ readyOrders.length }})
        </a>
        <a role="tab" :class="['tab', activeTab === 'served' ? 'tab-active bg-success text-white' : '']" @click="activeTab = 'served'">
          💰 เช็คบิล ({{ servedOrders.length }})
        </a>
      </div>

      <div v-if="activeTab === 'ready'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-if="readyOrders.length === 0" class="col-span-full text-center py-10 opacity-50">
          <div class="text-6xl mb-2">✅</div>
          <div>เสิร์ฟครบหมดแล้ว! เก่งมาก</div>
        </div>

        <div v-for="order in readyOrders" :key="order.id" class="card bg-base-100 shadow-xl border-l-4 border-error">
          <div class="card-body p-4">
            <h2 class="card-title justify-between">
              โต๊ะ {{ order.table?.name }}
              <div class="badge badge-error text-white animate-pulse">ด่วน</div>
            </h2>
            <p class="text-xs text-gray-500 mb-2">ออเดอร์ #{{ order.invoice_number }}</p>

            <ul class="menu bg-base-200 rounded-box p-2 mb-4 text-sm">
              <li v-for="item in order.items" :key="item.id">
                <a class="flex justify-between cursor-default hover:bg-transparent">
                  <span>{{ item.product_name }}</span>
                  <span class="font-bold">x{{ item.quantity }}</span>
                </a>
              </li>
            </ul>

            <div class="card-actions justify-end">
              <button
                @click="markServed(order.id)"
                class="btn bg-gray-200 hover:bg-gray-300 text-slate-800 border-none w-full shadow-sm"
              >
                🏃‍♂️ นำไปเสิร์ฟ
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'served'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-if="servedOrders.length === 0" class="col-span-full text-center py-10 opacity-50">
          <div class="text-6xl mb-2">🍃</div>
          <div>ร้านว่าง... ยังไม่มีลูกค้า</div>
        </div>

        <div v-for="order in servedOrders" :key="order.id" class="card bg-white shadow-xl border border-slate-100">
          <div class="card-body p-5">
            <h2 class="card-title justify-between items-center pb-2 border-b border-slate-100">
              <span class="text-slate-700">โต๊ะ {{ order.table?.name }}</span>
              <div class="badge badge-success text-white">ทานอยู่</div>
            </h2>

            <div class="py-6 text-center">
               <span class="text-sm text-gray-500 block mb-1">ยอดสุทธิ</span>
               <span class="text-4xl font-black text-emerald-600">฿{{ Number(order.total_amount).toLocaleString() }}</span>
               <div class="text-xs text-gray-400 mt-2">({{ order.items.length }} รายการ)</div>
            </div>

            <div class="card-actions flex flex-col gap-3">
              <button @click="generateQR(order)" class="btn bg-blue-600 hover:bg-blue-700 text-white w-full text-lg shadow-md border-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                สแกนจ่าย (PromptPay)
              </button>

              <button @click="checkout(order.id)" class="btn bg-emerald-600 hover:bg-emerald-700 text-white w-full text-lg shadow-md border-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                รับเงินสด / ปิดโต๊ะ
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <dialog id="qr_modal" class="modal">
      <div class="modal-box text-center bg-white">
        <h3 class="font-bold text-xl mb-2 text-slate-800">📲 สแกนจ่ายเงิน (PromptPay)</h3>
        <p class="text-sm text-gray-500 mb-4">ร้านค้า: POS System ({{ currentQRTable }})</p>

        <div class="bg-white p-4 inline-block rounded-xl shadow-inner border border-slate-200 mb-4">
           <img :src="qrCodeUrl" alt="QR Code" class="w-64 h-64 mx-auto object-contain bg-white">
        </div>

        <div class="text-3xl font-black text-emerald-600 mb-2">฿{{ Number(currentQRTotal).toLocaleString() }}</div>
        <p class="text-xs text-red-500 mb-6">*กรุณาตรวจสอบสลิปเงินเข้าก่อนกดปิดโต๊ะ</p>

        <div class="modal-action flex justify-between gap-2 px-2">
          <form method="dialog" class="w-1/3">
            <button class="btn bg-gray-200 hover:bg-gray-300 text-slate-800 w-full border-none">ยกเลิก</button>
          </form>
          <button @click="confirmQRPayment" class="btn bg-green-600 hover:bg-green-700 text-white w-2/3 border-none text-lg">
             ✅ ได้รับเงินแล้ว
          </button>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-black/50"><button>close</button></form>
    </dialog>

    <dialog id="notification_modal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box bg-white border-l-8 border-green-500">
        <div class="text-center py-4">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4 animate-bounce">
            <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-1">อาหารเสร็จแล้ว! 🛎️</h3>
          <p class="text-lg text-gray-600">
            โต๊ะ <span class="text-3xl font-black text-green-600">{{ latestReadyOrder?.table?.name }}</span>
          </p>
          <p class="text-sm text-gray-400 mt-2">กรุณานำอาหารไปเสิร์ฟ</p>
        </div>
        <div class="modal-action justify-center">
          <form method="dialog">
            <button class="btn btn-success text-white w-48 text-lg shadow-lg shadow-green-200">
              รับทราบ 👌
            </button>
          </form>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-black/60"><button>close</button></form>
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
        <table style="width: 100%;">
          <tr v-for="item in printingOrder.items" :key="item.id">
            <td style="vertical-align: top;">{{ item.product_name }}<br>x{{ item.quantity }}</td>
            <td class="text-right" style="vertical-align: top;">{{ item.subtotal }}</td>
          </tr>
        </table>
        <div class="line"></div>
        <div class="text-right">
          <p class="bold" style="font-size: 16px; margin: 0;">ยอดรวม: {{ printingOrder.total_amount }}.-</p>
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

// ✏️ แก้ไขเลข PromptPay (เบอร์โทร/เลขบัตร ปชช)
const shopPromptPayId = "0812345678";

const orders = ref([]);
const activeTab = ref('ready');
const printingOrder = ref(null);
const currentQRTotal = ref(0);
const currentQRTable = ref('');
const currentOrderId = ref(null);
const qrCodeUrl = ref('');
let intervalId = null;

// --- ระบบแจ้งเตือน ---
const latestReadyOrder = ref(null);
const alertedOrderIds = ref(new Set()); // กันเตือนซ้ำ

// 🔊 ฟังก์ชันเล่นเสียง
const playNotificationSound = () => {
  const audio = new Audio('https://freesound.org/data/previews/320/320655_5260872-lq.mp3');
  audio.volume = 0.5;
  audio.play().catch(e => console.log('Click to enable audio first'));
}

const fetchOrders = async () => {
    try {
        const response = await axios.get('/api/waiter/orders');
        orders.value = response.data;
        checkNewReadyOrders(); // ✨ เช็คของใหม่
    } catch (e) { console.error(e); }
}

// ✨ เช็คออเดอร์ใหม่เพื่อแจ้งเตือน
const checkNewReadyOrders = () => {
    const readyItems = orders.value.filter(o => o.status === 'ready');

    readyItems.forEach(order => {
        if (!alertedOrderIds.value.has(order.id)) {
            alertedOrderIds.value.add(order.id);
            latestReadyOrder.value = order;

            // เปิด Popup + เล่นเสียง
            const modal = document.getElementById('notification_modal');
            if (modal) {
                modal.showModal();
                playNotificationSound();
            }
        }
    });
}

const readyOrders = computed(() => orders.value.filter(o => o.status === 'ready'));
const servedOrders = computed(() => orders.value.filter(o => o.status === 'served'));

const markServed = async (orderId) => {
    try {
        await axios.post(`/api/orders/${orderId}/status`, { status: 'served' });
        fetchOrders();
    } catch (e) { alert('Error'); }
}

const generateQR = (order) => {
    currentQRTotal.value = order.total_amount;
    currentQRTable.value = order.table?.name;
    currentOrderId.value = order.id;
    qrCodeUrl.value = `https://promptpay.io/${shopPromptPayId}/${order.total_amount}.png`;
    document.getElementById('qr_modal').showModal();
}

const confirmQRPayment = async () => {
    document.getElementById('qr_modal').close();
    await checkout(currentOrderId.value);
}

const printReceipt = async (order) => {
    printingOrder.value = order;
    await nextTick();
    window.print();
}

const checkout = async (orderId) => {
    const orderToPrint = orders.value.find(o => o.id === orderId);
    if(!document.getElementById('qr_modal').open) {
       if(!confirm('ยืนยันรับเงินและปิดโต๊ะ?')) return;
    }
    try {
        await printReceipt(orderToPrint);
        await axios.post(`/api/orders/${orderId}/checkout`);
        fetchOrders();
    } catch (e) { alert('Error'); }
}

const logout = async () => {
  if(!confirm('⚠️ คุณแน่ใจหรือไม่ว่าจะออกจากระบบ?')) return;
  try {
    await axios.post('/logout');
    window.location.href = '/login';
  } catch (e) {
    alert('Logout failed');
  }
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
