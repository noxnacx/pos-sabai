<template>
  <div data-theme="dark" class="min-h-screen bg-gray-900 p-4 font-sans">

    <div class="navbar bg-gray-800 rounded-2xl shadow-lg mb-4 border border-gray-700">

      <div class="flex-1 flex items-center gap-2 sm:gap-4">
        <div class="flex items-center gap-2 px-4">
            <span class="text-4xl">👨‍🍳</span>
            <div>
                <h1 class="text-xl font-bold text-white leading-tight">Kitchen Display</h1>
                <p class="text-xs text-gray-400">ระบบจัดการออเดอร์ในครัว</p>
            </div>
        </div>

        <div class="h-8 w-px bg-gray-600 mx-2"></div>

        <button @click="openStockModal" class="btn btn-error btn-sm text-white shadow-md bg-red-600 hover:bg-red-700 border-none">
          🚫 แจ้งของหมด
        </button>
      </div>

      <div class="flex-none gap-4">
        <div class="flex items-center gap-2 bg-gray-900/50 px-3 py-1.5 rounded-lg border border-gray-700">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-xs text-gray-400 font-mono">Live Update</span>
        </div>

        <button @click="logout" class="btn btn-ghost btn-sm text-gray-400 hover:text-white" title="ออกจากระบบ">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 01-3-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-[calc(100vh-110px)]">

      <div class="bg-gray-800 rounded-2xl p-4 overflow-y-auto border border-gray-700 shadow-xl relative flex flex-col">
        <div class="sticky top-0 bg-gray-800 z-10 pb-4 border-b border-gray-700 mb-4 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-red-400 flex items-center gap-2">
            🔔 มาใหม่ / รอทำ
            </h2>
            <div class="badge badge-error gap-2 font-bold text-white p-3">
                {{ pendingOrders.length }} รายการ
            </div>
        </div>

        <div v-if="pendingOrders.length === 0" class="flex-1 flex flex-col items-center justify-center opacity-30 text-center">
          <div class="text-8xl mb-4 grayscale">😴</div>
          <div class="text-xl font-bold text-gray-500">ยังไม่มีออเดอร์ใหม่</div>
        </div>

        <div
          v-for="order in pendingOrders"
          :key="order.id"
          class="card bg-gray-700 shadow-lg mb-4 border-l-4 border-red-500 hover:bg-gray-650 transition-colors animate-pulse-slow"
        >
          <div class="card-body p-5">
            <div class="flex justify-between items-start mb-2">
              <div>
                <h3 class="text-2xl font-black text-white flex items-center gap-2">
                    โต๊ะ {{ order.table?.name }}
                </h3>
                <div class="text-xs text-gray-400 mt-1 font-mono flex gap-2">
                   <span>#{{ order.invoice_number?.substring(0,8) }}</span>
                   <span>•</span>
                   <span>{{ formatTime(order.created_at) }}</span>
                </div>
              </div>

              <button
                @click="updateStatus(order.id, 'cooking')"
                class="btn bg-white text-black hover:bg-gray-200 border-none font-bold shadow-lg gap-2"
              >
                <span>🔥</span> รับงาน
              </button>

            </div>

            <div class="bg-gray-800/50 rounded-xl p-3 border border-gray-600/50">
                <ul class="space-y-2">
                <li v-for="item in order.items" :key="item.id" class="flex justify-between items-center p-2 rounded hover:bg-gray-700/50">
                    <span class="text-gray-200 text-lg font-medium">{{ item.product_name }}</span>
                    <span class="font-black text-2xl text-red-400">x{{ item.quantity }}</span>
                </li>
                </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-gray-800 rounded-2xl p-4 overflow-y-auto border border-gray-700 shadow-xl relative flex flex-col">
        <div class="sticky top-0 bg-gray-800 z-10 pb-4 border-b border-gray-700 mb-4 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-yellow-400 flex items-center gap-2">
            🍳 กำลังปรุง
            </h2>
            <div class="badge badge-warning gap-2 font-bold text-black p-3">
                {{ cookingOrders.length }} รายการ
            </div>
        </div>

        <div v-if="cookingOrders.length === 0" class="flex-1 flex flex-col items-center justify-center opacity-30 text-center">
           <div class="text-8xl mb-4 grayscale">👨‍🍳</div>
           <div class="text-xl font-bold text-gray-500">ครัวว่าง... รอรับงาน!</div>
        </div>

        <div
          v-for="order in cookingOrders"
          :key="order.id"
          class="card bg-gray-700 shadow-lg mb-4 border-l-4 border-yellow-400"
        >
          <div class="card-body p-5">
            <div class="flex justify-between items-start mb-2">
              <div>
                <h3 class="text-2xl font-black text-white">โต๊ะ {{ order.table?.name }}</h3>
                <div class="text-xs text-yellow-200/70 mt-1">เริ่มทำ: {{ formatTime(order.updated_at) }}</div>
              </div>
              <button @click="updateStatus(order.id, 'ready')" class="btn btn-success text-white shadow-lg shadow-green-900/20 border-none font-bold gap-2">
                ✅ เสร็จแล้ว
              </button>
            </div>

            <div class="bg-gray-800/50 rounded-xl p-3 border border-gray-600/50">
                <ul class="space-y-2">
                <li v-for="item in order.items" :key="item.id" class="flex justify-between items-center p-2 rounded hover:bg-gray-700/50">
                    <span class="text-gray-300 text-lg">{{ item.product_name }}</span>
                    <span class="font-bold text-2xl text-yellow-400">x{{ item.quantity }}</span>
                </li>
                </ul>
            </div>
          </div>
        </div>
      </div>

    </div>

    <dialog id="stock_modal" class="modal">
      <div class="modal-box bg-gray-800 border border-gray-600 text-white">
        <h3 class="font-bold text-xl mb-4 text-red-400 flex items-center gap-2">
           จัดการเมนูหมด (Out of Stock)
        </h3>
        <div class="form-control">
           <input type="text" v-model="searchQuery" placeholder="🔍 ค้นหาเมนู..." class="input input-bordered bg-gray-900 border-gray-600 text-white w-full mb-4" />
        </div>

        <div class="max-h-60 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
           <div v-for="product in filteredProducts" :key="product.id" class="flex justify-between items-center p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors border border-gray-600">
              <span :class="{'opacity-50 line-through text-gray-500': !product.is_active, 'text-gray-200 font-medium': product.is_active}">
                  {{ product.name }}
              </span>
              <input
                type="checkbox"
                class="toggle toggle-success"
                :checked="product.is_active"
                @change="toggleProduct(product)"
              />
           </div>
        </div>

        <div class="modal-action">
          <form method="dialog">
            <button class="btn btn-ghost text-gray-400 hover:text-white">ปิดหน้าต่าง</button>
          </form>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-black/70"><button>close</button></form>
    </dialog>

    <dialog id="kitchen_notification_modal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box bg-gray-800 border border-gray-600 shadow-2xl p-8 text-center">

        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-500/20 mb-6 animate-bounce">
            <span class="text-6xl">🔔</span>
        </div>

        <h3 class="text-3xl font-black text-white mb-2">มีออเดอร์ใหม่เข้า!</h3>
        <p class="text-xl text-gray-300 mb-8">
            โต๊ะ <span class="text-4xl font-black text-red-400 block mt-2">{{ latestPendingOrder?.table?.name }}</span>
        </p>
        <p class="text-sm text-gray-500 mb-6">
             {{ latestPendingOrder?.items?.length }} รายการอาหารรอการปรุง
        </p>

        <div class="modal-action justify-center w-full">
          <form method="dialog" class="w-full">
            <button class="btn bg-green-500 hover:bg-green-600 text-white w-full text-xl font-bold h-16 rounded-xl border-none shadow-lg shadow-green-500/30">
              รับทราบ ลุยเลย! 🔥
            </button>
          </form>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-black/80"><button>close</button></form>
    </dialog>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';

const orders = ref([]);
const products = ref([]);
const searchQuery = ref('');
let intervalId = null;

// --- Notification Logic ---
const latestPendingOrder = ref(null);
const alertedOrderIds = ref(new Set());

const playNotificationSound = () => {
  const audio = new Audio('https://freesound.org/data/previews/320/320655_5260872-lq.mp3');
  audio.volume = 0.5;
  audio.play().catch(e => console.log('Browser blocked autoplay'));
}

const fetchOrders = async () => {
    try {
        const res = await axios.get('/api/kitchen/orders');
        orders.value = res.data;
        checkNewOrders();
    } catch (e) { console.error(e); }
}

const checkNewOrders = () => {
    const pendingItems = orders.value.filter(o => o.status === 'pending');
    pendingItems.forEach(order => {
        if (!alertedOrderIds.value.has(order.id)) {
            alertedOrderIds.value.add(order.id);
            latestPendingOrder.value = order;
            const modal = document.getElementById('kitchen_notification_modal');
            if (modal) {
                modal.showModal();
                playNotificationSound();
            }
        }
    });
}

const pendingOrders = computed(() => orders.value.filter(o => o.status === 'pending'));
const cookingOrders = computed(() => orders.value.filter(o => o.status === 'cooking'));

const updateStatus = async (id, status) => {
    try {
        await axios.post(`/api/orders/${id}/status`, { status });
        fetchOrders();
    } catch (e) { alert('Error'); }
}

// --- Stock Management ---
const openStockModal = async () => {
    try {
        const res = await axios.get('/api/products');
        products.value = Array.isArray(res.data) ? res.data : res.data.data;
        document.getElementById('stock_modal').showModal();
    } catch (e) { console.error(e); }
}

const filteredProducts = computed(() => {
    if (!searchQuery.value) return products.value;
    return products.value.filter(p => p.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const toggleProduct = async (product) => {
    product.is_active = !product.is_active;
    try {
        await axios.post(`/api/products/${product.id}`, { is_active: product.is_active ? 1 : 0 });
    } catch (e) {
        alert('แก้ไขไม่สำเร็จ');
        product.is_active = !product.is_active;
    }
}

const logout = async () => {
  if(!confirm('⚠️ คุณแน่ใจหรือไม่ว่าจะออกจากระบบ?')) return;
  try {
    await axios.post('/logout');
    window.location.href = '/login';
  } catch (e) { alert('Logout failed'); }
}

const formatTime = (date) => new Date(date).toLocaleTimeString('th-TH', {hour:'2-digit', minute:'2-digit'});

onMounted(() => {
    fetchOrders();
    intervalId = setInterval(fetchOrders, 5000);
});

onUnmounted(() => clearInterval(intervalId));
</script>

<style scoped>
/* Animation ให้การ์ดใหม่กระพริบนิดๆ */
@keyframes pulse-slow {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.01); }
}
.animate-pulse-slow {
  animation: pulse-slow 3s infinite;
}
</style>
