<template>
  <div data-theme="dark" class="min-h-screen bg-gray-900 p-4 font-sans text-gray-100">

    <div class="navbar bg-gray-800 rounded-2xl shadow-lg mb-6 border border-gray-700 p-3">

      <div class="flex-1 flex items-center gap-4">
        <div class="flex items-center gap-3 px-2">
            <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center text-3xl">👨‍🍳</div>
            <div>
                <h1 class="text-xl font-bold text-white leading-none">Kitchen Display</h1>
                <p class="text-xs text-gray-400 mt-1">ระบบจัดการออเดอร์หลังร้าน</p>
            </div>
        </div>

        <div class="h-10 w-px bg-gray-700 mx-2"></div>


      </div>

      <div class="flex-none gap-4">
        <div class="hidden sm:flex items-center gap-2 bg-black/30 px-4 py-2 rounded-full border border-gray-700/50">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-xs text-green-400 font-mono font-bold">LIVE SYSTEM</span>
        </div>

        <button @click="logout" class="btn btn-ghost btn-sm btn-square text-gray-400 hover:text-white hover:bg-gray-700" title="ออกจากระบบ">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 01-3-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-[calc(100vh-120px)]">

      <div class="flex flex-col bg-gray-800 rounded-2xl border border-gray-700 shadow-xl overflow-hidden">
        <div class="p-4 bg-gray-800/95 backdrop-blur border-b border-gray-700 sticky top-0 z-10 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-3 h-8 bg-red-500 rounded-full"></div>
                <h2 class="text-2xl font-bold text-white">มาใหม่ / รอทำ</h2>
            </div>
            <div class="badge badge-error badge-lg font-bold text-white shadow-lg">{{ pendingOrders.length }}</div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
            <div v-if="pendingOrders.length === 0" class="h-full flex flex-col items-center justify-center opacity-20">
                <span class="text-8xl grayscale mb-4">🔔</span>
                <span class="text-xl font-bold">ว่าง... ยังไม่มีงานเข้า</span>
            </div>

            <div v-for="order in pendingOrders" :key="order.id" class="card bg-gray-700 border-l-4 border-red-500 shadow-md hover:bg-gray-650 transition-all group animate-fade-in-up">
                <div class="card-body p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-2xl font-black text-white flex items-center gap-2">
                                🍽️ โต๊ะ {{ order.table?.name }}
                            </h3>
                            <p class="text-xs text-gray-400 font-mono mt-1">เวลา: {{ formatTime(order.created_at) }}</p>
                        </div>
                        <button
                            @click="updateStatus(order.id, 'cooking')"
                            class="btn bg-white hover:bg-gray-100 text-black border-none font-bold shadow-lg gap-2"
                        >
                            <span>🔥</span> เริ่มทำ
                        </button>
                    </div>

                    <div class="bg-black/20 rounded-xl p-1">
                        <ul class="divide-y divide-gray-600/30">
                            <li v-for="item in order.items" :key="item.id" class="flex justify-between items-center p-3 hover:bg-white/5 rounded-lg transition-colors">
                                <span class="text-lg text-gray-200 font-medium">{{ item.product_name }}</span>

                                <div class="flex items-center gap-2 bg-gray-800 rounded-lg p-1 border border-gray-600">
                                    <button @click="adjustKitchenQty(item, -1)" class="w-8 h-8 flex items-center justify-center bg-gray-700 hover:bg-red-900/80 text-red-400 rounded font-bold transition-colors">{{ item.quantity === 1 ? '🗑️' : '-' }}</button>
                                    <span class="text-xl font-black text-white min-w-[30px] text-center">{{ item.quantity }}</span>
                                    <button @click="adjustKitchenQty(item, 1)" class="w-8 h-8 flex items-center justify-center bg-gray-700 hover:bg-green-900/80 text-green-400 rounded font-bold transition-colors">+</button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <div class="flex flex-col bg-gray-800 rounded-2xl border border-gray-700 shadow-xl overflow-hidden">
        <div class="p-4 bg-gray-800/95 backdrop-blur border-b border-gray-700 sticky top-0 z-10 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-3 h-8 bg-yellow-400 rounded-full"></div>
                <h2 class="text-2xl font-bold text-white">กำลังปรุง</h2>
            </div>
            <div class="badge badge-warning badge-lg font-bold text-black shadow-lg">{{ cookingOrders.length }}</div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
            <div v-if="cookingOrders.length === 0" class="h-full flex flex-col items-center justify-center opacity-20">
                <span class="text-8xl grayscale mb-4">🍳</span>
                <span class="text-xl font-bold">รอรับงานมาทำ...</span>
            </div>

            <div v-for="order in cookingOrders" :key="order.id" class="card bg-gray-700 border-l-4 border-yellow-400 shadow-md">
                <div class="card-body p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-2xl font-black text-white">โต๊ะ {{ order.table?.name }}</h3>
                            <p class="text-xs text-yellow-200/80 mt-1">เริ่มเมื่อ: {{ formatTime(order.updated_at) }}</p>
                        </div>
                        <button
                            @click="updateStatus(order.id, 'ready')"
                            class="btn btn-success text-white border-none font-bold shadow-lg shadow-green-900/20 gap-2"
                        >
                            ✅ เสร็จแล้ว
                        </button>
                    </div>

                    <div class="bg-black/20 rounded-xl p-3">
                        <ul class="space-y-2">
                            <li v-for="item in order.items" :key="item.id" class="flex justify-between items-center">
                                <span class="text-lg text-gray-300">{{ item.product_name }}</span>
                                <span class="text-2xl font-bold text-yellow-400">x{{ item.quantity }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
      </div>

    </div>

    <dialog id="stock_modal" class="modal backdrop-blur-sm">
      <div class="modal-box bg-gray-800 border border-gray-600 text-white max-w-lg shadow-2xl">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-700">
            <h3 class="font-bold text-xl text-red-400 flex items-center gap-3">
               <span class="text-2xl">🛑</span> จัดการเมนูหมด / ปิดการขาย
            </h3>
            <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost text-gray-400 hover:bg-gray-700 hover:text-white">✕</button></form>
        </div>

        <div class="relative mb-4">
           <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">🔍</span>
           <input type="text" v-model="searchQuery" placeholder="ค้นหาชื่อเมนู..." class="input input-bordered bg-gray-900 border-gray-600 text-white w-full pl-10 focus:border-red-500 focus:ring-1 focus:ring-red-500" />
        </div>

        <div class="max-h-[50vh] overflow-y-auto space-y-2 pr-2 custom-scrollbar">
           <div v-for="product in filteredProducts" :key="product.id"
                class="flex justify-between items-center p-4 rounded-xl border transition-all duration-200"
                :class="product.is_active ? 'bg-gray-700/50 border-gray-600' : 'bg-red-900/10 border-red-900/30 opacity-60'">

              <div class="flex items-center gap-3">
                  <div class="w-3 h-3 rounded-full shadow-sm" :class="product.is_active ? 'bg-green-500 shadow-green-500/50' : 'bg-red-500 shadow-red-500/50'"></div>
                  <div class="flex flex-col">
                      <span class="text-base font-medium" :class="{'text-gray-200': product.is_active, 'text-gray-500 line-through decoration-red-500/50': !product.is_active}">
                          {{ product.name }}
                      </span>
                      <span class="text-[10px] uppercase font-bold tracking-wider" :class="product.is_active ? 'text-green-500' : 'text-red-500'">
                          {{ product.is_active ? 'พร้อมขาย' : 'ของหมด (Closed)' }}
                      </span>
                  </div>
              </div>

              <input
                type="checkbox"
                class="toggle toggle-success toggle-lg"
                :checked="product.is_active"
                @change="toggleProduct(product)"
              />
           </div>
        </div>

        <div class="modal-action border-t border-gray-700 pt-4 mt-4">
            <form method="dialog" class="w-full">
                <button class="btn btn-block bg-gray-700 hover:bg-gray-600 text-white border-none">ปิดหน้าต่าง</button>
            </form>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-black/80"><button>close</button></form>
    </dialog>

    <dialog id="kitchen_notification_modal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box bg-gray-800 border border-gray-600 shadow-2xl p-8 text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500"></div>

        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gray-700/50 mb-6 animate-bounce border-4 border-gray-600">
            <span class="text-6xl">🔔</span>
        </div>

        <h3 class="text-3xl font-black text-white mb-2 tracking-tight">มีออเดอร์ใหม่เข้า!</h3>
        <p class="text-xl text-gray-300 mb-8 font-light">
            โต๊ะ <span class="text-4xl font-black text-red-400 block mt-3">{{ latestPendingOrder?.table?.name }}</span>
        </p>
        <p class="text-sm text-gray-500 mb-8 bg-black/20 py-2 rounded-lg">
             {{ latestPendingOrder?.items?.length }} รายการอาหารรอการปรุง
        </p>

        <div class="modal-action justify-center w-full">
          <form method="dialog" class="w-full">
            <button class="btn bg-green-500 hover:bg-green-400 text-white w-full text-xl font-bold h-16 rounded-2xl border-none shadow-[0_0_20px_rgba(34,197,94,0.4)] transition-all hover:scale-[1.02]">
              รับทราบ ลุยเลย! 🔥
            </button>
          </form>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-black/90"><button>close</button></form>
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
  audio.volume = 0.6;
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

const adjustKitchenQty = async (item, change) => {
    const newQty = item.quantity + change;
    if (newQty <= 0) {
        if(!confirm(`❌ ต้องการลบเมนู "${item.product_name}" ออกจากออเดอร์นี้ใช่ไหม?`)) return;
    }
    try {
        await axios.post(`/api/order-items/${item.id}/update`, { quantity: newQty });
        fetchOrders();
    } catch (e) { alert('แก้ไขไม่สำเร็จ: ' + (e.response?.data?.message || 'Error')); }
}

// --- Stock Management ---
const openStockModal = async () => {
    try {
        // เรียก API ดึงสินค้าทั้งหมด (รวมที่ปิดอยู่ด้วย)
        const res = await axios.get('/api/products');
        products.value = Array.isArray(res.data) ? res.data : res.data.data;
        document.getElementById('stock_modal').showModal();
    } catch (e) { console.error(e); }
}

const filteredProducts = computed(() => {
    if (!searchQuery.value) return products.value;
    return products.value.filter(p => p.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

// ✨ Toggle Product Status (ใช้ Route ใหม่)
const toggleProduct = async (product) => {
    // 1. จำค่าเดิมไว้เผื่อ error
    const originalState = product.is_active;
    // 2. เปลี่ยนค่าในหน้าจอทันที (Optimistic UI)
    product.is_active = !originalState;

    try {
        // 3. ยิงไปที่ Route ใหม่ที่สร้างไว้ (toggle-status)
        await axios.post(`/api/products/${product.id}/toggle-status`, {
            is_active: product.is_active ? 1 : 0
        });
    } catch (e) {
        // 4. ถ้า Error ให้ดีดค่ากลับ และแจ้งเตือน
        product.is_active = originalState;
        alert('แก้ไขสถานะไม่สำเร็จ (ตรวจสอบ API /toggle-status)');
        console.error(e);
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
.animate-pulse-slow { animation: pulse-slow 3s infinite; }
@keyframes pulse-slow {
  0%, 100% { transform: scale(1); border-color: rgba(239, 68, 68, 0.8); }
  50% { transform: scale(1.01); border-color: rgba(239, 68, 68, 0.4); }
}

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fade-in-up 0.3s ease-out; }

.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #1f2937; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #6b7280; }
</style>
