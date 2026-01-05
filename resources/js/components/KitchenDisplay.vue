<template>
  <div data-theme="dark" class="min-h-screen bg-base-300 p-4">

    <div class="navbar bg-base-100 rounded-box shadow-lg mb-4">
      <div class="flex-1">
        <a class="btn btn-ghost normal-case text-xl text-warning">
          👨‍🍳 ห้องครัว (Kitchen)
        </a>
      </div>
      <div class="flex-none gap-2">
        <div class="text-xs text-gray-500 mr-2 hidden sm:block">Auto-refresh: 5s</div>

        <button @click="openStockModal" class="btn btn-error btn-sm text-white">
          🚫 แจ้งของหมด
        </button>

        <button @click="logout" class="btn btn-ghost btn-sm text-gray-400 hover:text-white" title="ออกจากระบบ">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 01-3-3h4a3 3 0 013 3v1" /></svg>
          <span class="hidden sm:inline">ออก</span>
        </button>

      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 h-[calc(100vh-100px)]">

      <div class="bg-base-200 rounded-xl p-4 overflow-y-auto border-t-4 border-error">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-error">
          🔔 มาใหม่ / รอทำ
          <div class="badge badge-error text-white">{{ pendingOrders.length }}</div>
        </h2>

        <div v-if="pendingOrders.length === 0" class="text-center py-10 opacity-50">
          <div class="text-6xl mb-2">😴</div>
          <div>ว่างจัง... ยังไม่มีงานเข้า</div>
        </div>

        <div
          v-for="order in pendingOrders"
          :key="order.id"
          class="card bg-base-100 shadow-sm mb-3 animate-pulse border border-base-300"
        >
          <div class="card-body p-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="card-title text-lg">โต๊ะ {{ order.table?.name }}</h3>
                <div class="text-xs opacity-70">เวลา: {{ formatTime(order.created_at) }}</div>
              </div>
              <button @click="updateStatus(order.id, 'cooking')" class="btn btn-warning btn-sm">
                🔥 รับงาน
              </button>
            </div>
            <div class="divider my-1"></div>
            <ul class="text-sm space-y-1">
              <li v-for="item in order.items" :key="item.id" class="flex justify-between">
                <span>{{ item.product_name }}</span>
                <span class="font-bold text-lg">x{{ item.quantity }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="bg-base-200 rounded-xl p-4 overflow-y-auto border-t-4 border-warning">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-warning">
          🍳 กำลังปรุง
          <div class="badge badge-warning text-white">{{ cookingOrders.length }}</div>
        </h2>

        <div
          v-for="order in cookingOrders"
          :key="order.id"
          class="card bg-base-100 shadow-sm mb-3 border-l-4 border-warning"
        >
          <div class="card-body p-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="card-title text-lg">โต๊ะ {{ order.table?.name }}</h3>
                <div class="text-xs opacity-70">เริ่มทำเมื่อ: {{ formatTime(order.updated_at) }}</div>
              </div>
              <button @click="updateStatus(order.id, 'ready')" class="btn btn-success text-white btn-sm">
                ✅ เสร็จแล้ว
              </button>
            </div>
            <div class="divider my-1"></div>
            <ul class="text-lg font-bold text-gray-300">
               <li v-for="item in order.items" :key="item.id" class="flex justify-between">
                <span>{{ item.product_name }}</span>
                <span>x{{ item.quantity }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

    </div>

    <dialog id="stock_modal" class="modal">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4 text-error">🚫 จัดการเมนูหมด (Out of Stock)</h3>
        <div class="form-control">
           <input type="text" v-model="searchQuery" placeholder="🔍 ค้นหาเมนู..." class="input input-bordered input-sm mb-4 w-full" />
        </div>

        <div class="max-h-60 overflow-y-auto space-y-2">
           <div v-for="product in filteredProducts" :key="product.id" class="flex justify-between items-center p-2 bg-base-200 rounded">
              <span :class="{'opacity-50 line-through': !product.is_active}">{{ product.name }}</span>
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
            <button class="btn">ปิดหน้าต่าง</button>
          </form>
        </div>
      </div>
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

// --- โซน Order ---
const fetchOrders = async () => {
    try {
        const res = await axios.get('/api/kitchen/orders');
        orders.value = res.data;
    } catch (e) { console.error(e); }
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
        // ต้องเช็คว่า API คืนค่ามาเป็น array ตรงๆ หรืออยู่ใน data
        products.value = Array.isArray(res.data) ? res.data : res.data.data;
        document.getElementById('stock_modal').showModal();
    } catch (e) { console.error(e); }
}

const filteredProducts = computed(() => {
    if (!searchQuery.value) return products.value;
    return products.value.filter(p => p.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const toggleProduct = async (product) => {
    // เปลี่ยนค่าทันทีให้ดูไว
    product.is_active = !product.is_active;
    try {
        // ยิง API บันทึก (ส่ง 1/0 แทน true/false เพื่อความชัวร์กับบาง DB)
        await axios.post(`/api/products/${product.id}`, {
            is_active: product.is_active ? 1 : 0
        });
    } catch (e) {
        alert('แก้ไขไม่สำเร็จ');
        product.is_active = !product.is_active; // คืนค่าเดิมถ้าพัง
    }
}

// --- โซน Logout (เพิ่มใหม่) ---
const logout = async () => {
  if(!confirm('ยืนยันออกจากระบบครัว?')) return;
  try {
    await axios.post('/logout'); // เรียก API Logout
    window.location.href = '/login'; // เด้งกลับไปหน้า Login
  } catch (e) {
    alert('Logout failed');
  }
}

// Helper เวลา
const formatTime = (date) => new Date(date).toLocaleTimeString('th-TH', {hour:'2-digit', minute:'2-digit'});

onMounted(() => {
    fetchOrders();
    intervalId = setInterval(fetchOrders, 5000); // อัปเดตทุก 5 วิ
});

onUnmounted(() => clearInterval(intervalId));
</script>
