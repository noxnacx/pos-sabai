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
              <button @click="markServed(order.id)" class="btn btn-error text-white w-full">
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

        <div v-for="order in servedOrders" :key="order.id" class="card bg-base-100 shadow-xl border-l-4 border-success">
          <div class="card-body p-4">
            <h2 class="card-title justify-between">
              โต๊ะ {{ order.table?.name }}
              <div class="badge badge-success text-white">Active</div>
            </h2>
            <div class="flex justify-between items-end my-4 border-b pb-2">
               <span class="text-sm">ยอดรวม</span>
               <span class="text-3xl font-bold text-success">฿{{ order.total_amount }}</span>
            </div>

            <div class="card-actions grid grid-cols-2 gap-2">
              <button @click="generateQR(order)" class="btn btn-outline btn-info btn-sm">
                📲 QR รับเงิน
              </button>
              <button @click="checkout(order.id)" class="btn btn-success text-white btn-sm">
                💵 เงินสด/ปิดโต๊ะ
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <dialog id="qr_modal" class="modal">
      <div class="modal-box text-center">
        <h3 class="font-bold text-lg mb-4">📲 สแกนจ่ายเงิน (PromptPay)</h3>

        <div class="bg-white p-4 inline-block rounded-lg shadow-inner mb-4 border">
           <img src="https://via.placeholder.com/200?text=QR+Code" alt="QR Code" class="w-48 h-48 mx-auto">
        </div>

        <div class="text-2xl font-bold text-primary mb-2">฿{{ currentQRTotal }}</div>
        <p class="text-sm text-gray-500">โต๊ะ {{ currentQRTable }}</p>

        <div class="modal-action justify-center">
          <form method="dialog">
            <button class="btn btn-wide">ปิดหน้าต่าง</button>
          </form>
        </div>
      </div>
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

const orders = ref([]);
const activeTab = ref('ready'); // 'ready' or 'served'
const printingOrder = ref(null);
const currentQRTotal = ref(0);
const currentQRTable = ref('');
let intervalId = null;

// --- Data Fetching ---
const fetchOrders = async () => {
    try {
        const response = await axios.get('/api/waiter/orders');
        orders.value = response.data;
    } catch (e) { console.error(e); }
}

const readyOrders = computed(() => orders.value.filter(o => o.status === 'ready'));
const servedOrders = computed(() => orders.value.filter(o => o.status === 'served'));

// --- Actions ---
const markServed = async (orderId) => {
    try {
        await axios.post(`/api/orders/${orderId}/status`, { status: 'served' });
        fetchOrders();
    } catch (e) { alert('Error'); }
}

const generateQR = (order) => {
    currentQRTotal.value = order.total_amount;
    currentQRTable.value = order.table?.name;
    document.getElementById('qr_modal').showModal();
}

// Print & Checkout Logic
const printReceipt = async (order) => {
    printingOrder.value = order;
    await nextTick();
    window.print();
}

const checkout = async (orderId) => {
    const orderToPrint = orders.value.find(o => o.id === orderId);
    if(!confirm('ยืนยันปิดโต๊ะและรับเงิน?')) return;

    try {
        await printReceipt(orderToPrint); // พิมพ์ก่อน
        await axios.post(`/api/orders/${orderId}/checkout`); // แล้วค่อยปิดงาน
        fetchOrders();
    } catch (e) { alert('Error'); }
}

// --- โซน Logout (เพิ่มใหม่) ---
const logout = async () => {
  if(!confirm('ยืนยันออกจากระบบ?')) return;
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
/* CSS ใบเสร็จเหมือนเดิมเป๊ะ */
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
