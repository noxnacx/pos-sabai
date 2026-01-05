<template>
  <!-- ใช้ Theme Cupcake หรือ Light ให้ดูสะอาดตา -->
  <div data-theme="cupcake" class="min-h-screen bg-base-200 pb-24">

    <!-- 🟢 Header (Sticky Top) -->
    <div class="navbar bg-base-100 shadow-md sticky top-0 z-50">
      <div class="flex-1">
        <a class="btn btn-ghost normal-case text-xl text-primary">
          🍽️ โต๊ะ {{ tableId }}
        </a>
      </div>
      <div class="flex-none">
        <button class="btn btn-circle btn-ghost" @click="fetchProducts">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
        </button>
      </div>
    </div>

    <!-- 🟡 Categories (Scrollable) -->
    <div class="bg-base-100 p-2 shadow-sm sticky top-16 z-40 overflow-x-auto whitespace-nowrap scrollbar-hide">
      <div class="flex gap-2 px-2">
        <button
          @click="activeCategory = 0"
          :class="['btn btn-sm rounded-full', activeCategory === 0 ? 'btn-primary' : 'btn-outline']"
        >
          ทั้งหมด
        </button>
        <button
          v-for="cat in categories"
          :key="cat.id"
          @click="activeCategory = cat.id"
          :class="['btn btn-sm rounded-full', activeCategory === cat.id ? 'btn-primary' : 'btn-outline']"
        >
          {{ cat.name }}
        </button>
      </div>
    </div>

    <!-- 🍔 Products Grid -->
    <div class="p-4">
      <div v-if="loading" class="flex justify-center mt-10">
        <span class="loading loading-spinner loading-lg text-primary"></span>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="card bg-base-100 shadow-xl compact"
        >
          <figure class="h-40 overflow-hidden">
            <img :src="product.image_url || 'https://via.placeholder.com/300x200'" class="w-full h-full object-cover" />
          </figure>
          <div class="card-body p-4">
            <h2 class="card-title text-base">
              {{ product.name }}
              <div class="badge badge-secondary" v-if="!product.is_active">หมด</div>
            </h2>
            <p class="text-sm text-gray-500">{{ product.category?.name }}</p>

            <div class="card-actions justify-between items-center mt-2">
              <div class="text-lg font-bold text-primary">฿{{ product.price }}</div>

              <!-- ปุ่มบวกลบ -->
              <div v-if="product.is_active">
                 <div v-if="getItemQty(product.id) > 0" class="flex items-center gap-2">
                    <button @click="removeFromCart(product)" class="btn btn-circle btn-xs btn-outline">-</button>
                    <span class="font-bold">{{ getItemQty(product.id) }}</span>
                    <button @click="addToCart(product)" class="btn btn-circle btn-xs btn-primary text-white">+</button>
                 </div>
                 <button v-else @click="addToCart(product)" class="btn btn-sm btn-primary text-white rounded-full">
                   + เพิ่ม
                 </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 🦶 Bottom Navigation Bar (Fixed) -->
    <div class="btm-nav shadow-lg z-50 bg-white border-t">
      <!-- ปุ่มดูออเดอร์เก่า -->
      <button @click="openHistoryModal" class="text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
        <span class="btm-nav-label text-xs">สถานะอาหาร</span>
      </button>

      <!-- ปุ่มดูตะกร้า (ตัวใหญ่ตรงกลาง) -->
      <button
        @click="cart.length > 0 ? openCartModal() : null"
        :class="['active border-blue-500', cart.length > 0 ? 'bg-blue-50 text-blue-600' : 'disabled text-gray-400']"
      >
        <div class="indicator">
          <span v-if="cartItemCount > 0" class="indicator-item badge badge-secondary badge-sm">{{ cartItemCount }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        </div>
        <span class="btm-nav-label text-xs font-bold">ตะกร้า (฿{{ totalAmount }})</span>
      </button>

      <!-- ปุ่มเรียกพนักงาน (จำลอง) -->
      <button class="text-gray-600" @click="alert('เรียกพนักงานสักครู่ครับ')">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
        <span class="btm-nav-label text-xs">เรียกพนักงาน</span>
      </button>
    </div>

    <!-- 🛒 Modal ตะกร้าสินค้า -->
    <dialog id="cart_modal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box">
        <h3 class="font-bold text-lg">🛒 ตะกร้าสินค้าของคุณ</h3>

        <div v-if="cart.length === 0" class="py-10 text-center text-gray-500">ว่างเปล่า...</div>

        <ul class="menu bg-base-100 w-full p-0">
           <li v-for="(item, index) in cart" :key="index" class="border-b">
             <div class="flex justify-between items-center py-2">
                <div>
                   <div class="font-bold">{{ item.name }}</div>
                   <div class="text-xs text-gray-500">฿{{ item.price }} x {{ item.qty }}</div>
                </div>
                <div class="flex items-center gap-2">
                   <div class="font-bold">฿{{ item.price * item.qty }}</div>
                   <button @click="removeFromCart(item)" class="btn btn-xs btn-circle btn-error text-white">x</button>
                </div>
             </div>
           </li>
        </ul>

        <div class="flex justify-between items-center mt-4 border-t pt-4">
           <span class="text-lg">ยอดรวมสุทธิ</span>
           <span class="text-xl font-bold text-primary">฿{{ totalAmount }}</span>
        </div>

        <div class="modal-action">
          <form method="dialog">
            <button class="btn">ปิด</button>
          </form>
          <button @click="submitOrder" class="btn btn-primary text-white">ยืนยันสั่งอาหาร</button>
        </div>
      </div>
    </dialog>

    <!-- 📋 Modal ประวัติการสั่ง/สถานะ -->
    <dialog id="history_modal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box">
        <h3 class="font-bold text-lg mb-4">🕒 รายการที่สั่งไปแล้ว</h3>

        <div v-if="orderHistory.length === 0" class="text-center py-10 text-gray-400">ยังไม่มีรายการที่สั่ง</div>

        <div v-for="order in orderHistory" :key="order.id" class="collapse collapse-arrow bg-base-100 border mb-2">
            <input type="checkbox" />
            <div class="collapse-title flex justify-between items-center pr-10">
               <span class="font-bold text-sm">#{{ order.invoice_number.substring(0,8) }}</span>
               <div :class="['badge badge-sm text-white', getStatusColor(order.status)]">
                 {{ getStatusText(order.status) }}
               </div>
            </div>
            <div class="collapse-content">
               <ul class="text-sm">
                 <li v-for="item in order.items" :key="item.id" class="flex justify-between py-1 border-b border-dashed">
                    <span>{{ item.product_name }} x{{ item.quantity }}</span>
                    <span>฿{{ item.subtotal }}</span>
                 </li>
               </ul>
               <div class="text-right font-bold mt-2">รวม: ฿{{ order.total_amount }}</div>
            </div>
        </div>

        <div class="modal-action">
          <form method="dialog">
            <button class="btn">ปิด</button>
          </form>
        </div>
      </div>
    </dialog>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps(['tableId']);
const products = ref([]);
const categories = ref([]); // เก็บหมวดหมู่
const activeCategory = ref(0); // 0 = ทั้งหมด

const cart = ref([]);
const orderHistory = ref([]);
const loading = ref(true);

// โหลดข้อมูล (สินค้า + หมวดหมู่)
const fetchProducts = async () => {
    loading.value = true;
    try {
        const [prodRes, catRes] = await Promise.all([
            axios.get('/api/products'),
            axios.get('/api/categories')
        ]);
        products.value = prodRes.data.data;
        categories.value = catRes.data;
    } catch (e) { console.error(e); }
    finally { loading.value = false; }
}

// โหลดประวัติ (เมื่อกดปุ่ม)
const fetchHistory = async () => {
    try {
        const res = await axios.get(`/api/orders/table/${props.tableId}`);
        orderHistory.value = res.data;
    } catch (e) { console.error(e); }
}

// เปิด Modal
const openCartModal = () => document.getElementById('cart_modal').showModal();
const openHistoryModal = () => {
    fetchHistory();
    document.getElementById('history_modal').showModal();
}

// Filter สินค้าตามหมวดหมู่
const filteredProducts = computed(() => {
    if (activeCategory.value === 0) return products.value;
    return products.value.filter(p => p.category_id === activeCategory.value);
});

// Logic ตะกร้า (เหมือนเดิม)
const getItemQty = (id) => cart.value.find(i => i.id === id)?.qty || 0;
const addToCart = (p) => {
    const item = cart.value.find(i => i.id === p.id);
    if (item) item.qty++;
    else cart.value.push({ ...p, qty: 1 });
};
const removeFromCart = (p) => {
    const idx = cart.value.findIndex(i => i.id === p.id);
    if (idx !== -1) {
        if (cart.value[idx].qty > 1) cart.value[idx].qty--;
        else cart.value.splice(idx, 1);
    }
};

const totalAmount = computed(() => cart.value.reduce((sum, i) => sum + (i.price * i.qty), 0));
const cartItemCount = computed(() => cart.value.reduce((sum, i) => sum + i.qty, 0));

const submitOrder = async () => {
    try {
        await axios.post('/api/orders', {
            table_id: props.tableId,
            items: cart.value.map(i => ({ id: i.id, qty: i.qty }))
        });
        alert('✅ ส่งออเดอร์เรียบร้อย!');
        cart.value = [];
        document.getElementById('cart_modal').close();
    } catch (e) { alert('Error'); }
};

// Helper แสดงสี/ชื่อสถานะ
const getStatusColor = (s) => {
    if(s === 'pending') return 'badge-error';
    if(s === 'cooking') return 'badge-warning';
    if(s === 'ready') return 'badge-success';
    if(s === 'served') return 'badge-info';
    return 'badge-ghost';
}
const getStatusText = (s) => {
    const map = { pending: 'รอคิว', cooking: 'กำลังทำ', ready: 'เสร็จแล้ว', served: 'เสิร์ฟแล้ว' };
    return map[s] || s;
}

onMounted(() => {
    fetchProducts();
});
</script>


