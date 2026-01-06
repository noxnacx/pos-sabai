<template>
  <div data-theme="dark" class="min-h-screen bg-base-300 p-4">

    <div class="navbar bg-base-100 rounded-box shadow-lg mb-4">

      <div class="flex-1 flex items-center gap-2 sm:gap-4">
        <a class="btn btn-ghost normal-case text-xl text-warning px-2 sm:px-4">
          👨‍🍳 ห้องครัว
        </a>

        <button @click="openStockModal" class="btn btn-error btn-sm text-white shadow-md border-2 border-white/10">
          🚫 แจ้งของหมด
        </button>
      </div>

      <div class="flex-none gap-4">
        <div class="text-xs text-gray-500 hidden sm:block font-mono bg-base-300 px-2 py-1 rounded">
          Auto-refresh: 5s
        </div>

        <button @click="logout" class="btn btn-ghost btn-sm text-gray-400 hover:text-white hover:bg-red-500/20" title="ออกจากระบบ">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 01-3-3h4a3 3 0 013 3v1" />
          </svg>
          <span class="hidden sm:inline ml-1">ออก</span>
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 h-[calc(100vh-100px)]">

      <div class="bg-base-200 rounded-xl p-4 overflow-y-auto border-t-4 border-error relative">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-error sticky top-0 bg-base-200 z-10 py-2">
          🔔 มาใหม่ / รอทำ
          <div class="badge badge-error text-white">{{ pendingOrders.length }}</div>
        </h2>

        <div v-if="pendingOrders.length === 0" class="flex flex-col items-center justify-center h-64 opacity-30 text-center">
          <div class="text-6xl mb-2">😴</div>
          <div>ว่างจัง... ยังไม่มีงานเข้า</div>
        </div>

        <div
          v-for="order in pendingOrders"
          :key="order.id"
          class="card bg-base-100 shadow-lg mb-3 animate-pulse border border-base-300 hover:scale-[1.01] transition-transform duration-200"
        >
          <div class="card-body p-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="card-title text-xl text-white">โต๊ะ {{ order.table?.name }}</h3>
                <div class="text-xs opacity-70 flex gap-2 mt-1">
                   <span class="bg-base-300 px-1 rounded">#{{ order.invoice_number?.substring(0,8) }}</span>
                   <span>เวลา: {{ formatTime(order.created_at) }}</span>
                </div>
              </div>

              <button
                @click="updateStatus(order.id, 'cooking')"
                class="btn bg-white text-black hover:bg-gray-200 border-none btn-sm font-bold shadow-md"
              >
                🔥 รับงาน
              </button>

            </div>
            <div class="divider my-1 before:bg-gray-600 after:bg-gray-600"></div>
            <ul class="text-sm space-y-2">
              <li v-for="item in order.items" :key="item.id" class="flex justify-between items-center bg-base-200/50 p-2 rounded">
                <span class="text-gray-200">{{ item.product_name }}</span>
                <span class="font-bold text-xl text-warning">x{{ item.quantity }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="bg-base-200 rounded-xl p-4 overflow-y-auto border-t-4 border-warning relative">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-warning sticky top-0 bg-base-200 z-10 py-2">
          🍳 กำลังปรุง
          <div class="badge badge-warning text-white">{{ cookingOrders.length }}</div>
        </h2>

        <div v-if="cookingOrders.length === 0" class="flex flex-col items-center justify-center h-64 opacity-30 text-center">
           <div class="text-6xl mb-2">👨‍🍳</div>
           <div>ครัวโล่ง... เร่งมือหน่อย!</div>
        </div>

        <div
          v-for="order in cookingOrders"
          :key="order.id"
          class="card bg-base-100 shadow-lg mb-3 border-l-4 border-warning"
        >
          <div class="card-body p-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="card-title text-xl text-white">โต๊ะ {{ order.table?.name }}</h3>
                <div class="text-xs opacity-70 mt-1">เริ่มทำ: {{ formatTime(order.updated_at) }}</div>
              </div>
              <button @click="updateStatus(order.id, 'ready')" class="btn btn-success text-white btn-sm shadow-green-500/50 shadow-sm">
                ✅ เสร็จแล้ว
              </button>
            </div>
            <div class="divider my-1 before:bg-gray-600 after:bg-gray-600"></div>
            <ul class="text-lg font-bold text-gray-300 space-y-2">
               <li v-for="item in order.items" :key="item.id" class="flex justify-between items-center p-2 rounded bg-base-200/30">
                <span>{{ item.product_name }}</span>
                <span class="text-warning">x{{ item.quantity }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

    </div>

    <dialog id="stock_modal" class="modal">
      <div class="modal-box bg-base-100 border border-gray-700">
        <h3 class="font-bold text-lg mb-4 text-error flex items-center gap-2">
           จัดการเมนูหมด (Out of Stock)
        </h3>
        <div class="form-control">
           <input type="text" v-model="searchQuery" placeholder="🔍 ค้นหาเมนู..." class="input input-bordered input-sm mb-4 w-full bg-base-200" />
        </div>

        <div class="max-h-60 overflow-y-auto space-y-2 pr-2">
           <div v-for="product in filteredProducts" :key="product.id" class="flex justify-between items-center p-3 bg-base-200 rounded-lg hover:bg-base-300 transition-colors">
              <span :class="{'opacity-50 line-through text-gray-500': !product.is_active, 'text-gray-200': product.is_active}">
                  {{ product.name }}
              </span>
              <input
                type="checkbox"
                class="toggle toggle-success toggle-sm"
                :checked="product.is_active"
                @change="toggleProduct(product)"
              />
           </div>
        </div>

        <div class="modal-action">
          <form method="dialog">
            <button class="btn btn-ghost">ปิดหน้าต่าง</button>
          </form>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-black/50"><button>close</button></form>
    </dialog>

    <dialog id="kitchen_notification_modal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box bg-base-100 border-l-8 border-error text-white">
        <div class="text-center py-4">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-900/50 mb-4 animate-bounce">
            <span class="text-4xl">🔔</span>
          </div>
          <h3 class="text-2xl font-bold mb-1 text-error">มีออเดอร์ใหม่เข้า!</h3>
          <p class="text-lg">
            โต๊ะ <span class="text-3xl font-black text-white">{{ latestPendingOrder?.table?.name }}</span>
          </p>
          <p class="text-sm text-gray-400 mt-2">
             {{ latestPendingOrder?.items?.length }} รายการอาหาร
          </p>
        </div>
        <div class="modal-action justify-center">
          <form method="dialog">
            <button class="btn btn-warning w-48 text-lg font-bold text-black shadow-lg shadow-orange-500/20">
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

// ✨ เพิ่มตัวแปรสำหรับระบบแจ้งเตือน
const latestPendingOrder = ref(null);
const alertedOrderIds = ref(new Set()); // เก็บ ID ออเดอร์ที่เตือนไปแล้ว

// --- Sound Effect (เสียงกริ่ง) ---
const playNotificationSound = () => {
  const audio = new Audio('https://freesound.org/data/previews/320/320655_5260872-lq.mp3');
  audio.volume = 0.5;
  audio.play().catch(e => console.log('Browser blocked autoplay, user interaction needed first.'));
}

// --- โซน Order ---
const fetchOrders = async () => {
    try {
        const res = await axios.get('/api/kitchen/orders');
        orders.value = res.data;
        checkNewOrders(); // ✨ เช็คออเดอร์ใหม่ทุกครั้งที่ดึงข้อมูล
    } catch (e) { console.error(e); }
}

// ✨ ฟังก์ชันเช็คและแจ้งเตือนออเดอร์ใหม่
const checkNewOrders = () => {
    const pendingItems = orders.value.filter(o => o.status === 'pending');

    pendingItems.forEach(order => {
        if (!alertedOrderIds.value.has(order.id)) {
            // เจอของใหม่!
            alertedOrderIds.value.add(order.id);
            latestPendingOrder.value = order;

            // เปิด Modal แจ้งเตือน
            const modal = document.getElementById('kitchen_notification_modal');
            if (modal) {
                modal.showModal();
                playNotificationSound(); // เล่นเสียงกริ่ง
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

// --- โซนจัดการเมนู (Stock) ---
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
        await axios.post(`/api/products/${product.id}`, {
            is_active: product.is_active ? 1 : 0
        });
    } catch (e) {
        alert('แก้ไขไม่สำเร็จ');
        product.is_active = !product.is_active;
    }
}

// --- โซน Logout ---
const logout = async () => {
  if(!confirm('⚠️ คุณแน่ใจหรือไม่ว่าจะออกจากระบบ?')) return;
  try {
    await axios.post('/logout');
    window.location.href = '/login';
  } catch (e) {
    alert('Logout failed');
  }
}

const formatTime = (date) => new Date(date).toLocaleTimeString('th-TH', {hour:'2-digit', minute:'2-digit'});

onMounted(() => {
    fetchOrders();
    intervalId = setInterval(fetchOrders, 5000);
});

onUnmounted(() => clearInterval(intervalId));
</script>
