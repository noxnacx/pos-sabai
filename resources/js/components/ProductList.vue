<template>
  <div class="min-h-screen bg-gray-50 font-sans pb-32">

    <div class="bg-white sticky top-0 z-40 px-4 py-3 shadow-sm flex justify-between items-center">
      <button @click="openHistoryModal" class="btn btn-circle btn-ghost btn-sm bg-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
        </svg>
      </button>

      <div class="text-center">
        <h1 class="text-lg font-bold text-gray-800">ร้านอาหารไทย</h1>
        <p class="text-xs text-gray-400">โต๊ะ {{ tableId || 'ทั่วไป' }}</p>
      </div>

      <div class="relative">
        <button @click="openCartModal" class="btn btn-circle btn-ghost btn-sm bg-orange-100 text-orange-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </button>
        <div v-if="cartItemCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full animate-bounce">
          {{ cartItemCount }}
        </div>
      </div>
    </div>

    <div class="px-4 py-4 bg-white">
      <div class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
          <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </span>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="ค้นหาเมนูอาหาร..."
          class="w-full py-2 pl-10 pr-4 text-gray-700 bg-gray-100 rounded-xl focus:outline-none focus:bg-white focus:ring-2 focus:ring-orange-200 transition-all"
        >
      </div>
    </div>

    <div class="bg-white pb-4 shadow-sm mb-2">
      <div class="flex overflow-x-auto px-4 gap-3 scrollbar-hide">
        <button
          @click="selectedCategory = 0"
          :class="['px-4 py-1.5 rounded-full whitespace-nowrap text-sm font-medium transition-all duration-200', selectedCategory === 0 ? 'bg-orange-500 text-white shadow-md shadow-orange-200 scale-105' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']"
        >
          ★ ทั้งหมด
        </button>
        <button
          v-for="cat in categories"
          :key="cat.id"
          @click="selectedCategory = cat.id"
          :class="['px-4 py-1.5 rounded-full whitespace-nowrap text-sm font-medium transition-all duration-200', selectedCategory === cat.id ? 'bg-orange-500 text-white shadow-md shadow-orange-200 scale-105' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']"
        >
          {{ cat.name }}
        </button>
      </div>
    </div>

    <div class="px-4 pt-2">
      <div v-if="loading" class="flex justify-center py-10">
        <span class="loading loading-dots loading-lg text-orange-500"></span>
      </div>

      <div v-else-if="filteredProducts.length === 0" class="text-center py-10 text-gray-400 flex flex-col items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <p>ไม่พบเมนูที่ค้นหา</p>
      </div>

      <div v-else class="space-y-4">
        <div v-for="product in filteredProducts" :key="product.id" class="flex bg-white p-3 rounded-2xl shadow-sm border border-gray-100 gap-4 transition-transform active:scale-[0.98]">

          <div class="w-28 h-28 flex-shrink-0 relative">
            <img
              :src="product.image_url || 'https://via.placeholder.com/150?text=No+Image'"
              class="w-full h-full object-cover rounded-xl"
              :class="{ 'grayscale': !product.is_active }"
            >
            <div v-if="!product.is_active" class="absolute inset-0 bg-black/50 rounded-xl flex items-center justify-center text-white font-bold text-sm">
                หมด
            </div>
          </div>

          <div class="flex-1 flex flex-col justify-between py-1">
            <div>
              <h3 class="text-base font-bold text-gray-800 line-clamp-1">{{ product.name }}</h3>
              <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ product.description || 'เมนูแนะนำ รสชาติอร่อย วัตถุดิบคุณภาพ' }}</p>
            </div>

            <div class="flex justify-between items-end mt-2">
              <span class="text-lg font-bold text-orange-500">
                ฿{{ product.price }}
              </span>

              <button
                v-if="product.is_active"
                @click="addToCart(product)"
                class="btn btn-circle btn-sm bg-orange-500 hover:bg-orange-600 border-none text-white shadow-md shadow-orange-200 active:scale-90 transition-transform"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="cart.length > 0" class="fixed bottom-0 left-0 right-0 bg-white border-t p-4 z-50 shadow-[0_-4px_20px_-5px_rgba(0,0,0,0.1)] pb-8">
      <div class="max-w-md mx-auto flex items-center justify-between">
        <div class="flex flex-col">
          <span class="text-xs text-gray-500">ยอดรวมทั้งหมด</span>
          <span class="text-2xl font-black text-gray-800">฿{{ totalAmount }}</span>
        </div>
        <button
          @click="openCartModal"
          class="btn bg-orange-500 hover:bg-orange-600 text-white border-none rounded-xl px-6 shadow-lg shadow-orange-200 flex items-center gap-2 animate-pulse hover:animate-none"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" /></svg>
          ตะกร้า ({{ cartItemCount }})
        </button>
      </div>
    </div>

    <dialog id="cart_modal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box bg-white p-0 overflow-hidden rounded-t-2xl sm:rounded-2xl">

        <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
           <h3 class="font-bold text-lg text-gray-800">🛒 ตะกร้าของฉัน</h3>
           <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost text-gray-400">✕</button></form>
        </div>

        <div class="p-4 bg-white max-h-[60vh] overflow-y-auto">
            <div v-if="cart.length === 0" class="text-center py-10 text-gray-400">ยังไม่มีสินค้าในตะกร้า</div>

            <div v-for="(item, index) in cart" :key="index" class="flex justify-between items-center border-b border-gray-100 py-3 last:border-0">
                <div class="flex gap-3 items-center">
                   <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                      <img :src="item.image_url" class="w-full h-full object-cover">
                   </div>
                   <div>
                       <div class="font-bold text-gray-800 text-sm line-clamp-1">{{ item.name }}</div>
                       <div class="text-xs text-gray-500">฿{{ item.price }} / ชิ้น</div>
                   </div>
                </div>

                <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
                    <button @click="decreaseQty(index)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 active:scale-90">-</button>
                    <span class="font-bold text-sm w-4 text-center">{{ item.qty }}</span>
                    <button @click="increaseQty(index)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-green-600 active:scale-90">+</button>
                </div>
            </div>
        </div>

        <div class="p-4 bg-gray-50 border-t" v-if="cart.length > 0">
            <div class="flex justify-between text-lg font-bold mb-4 text-gray-800">
                <span>รวมสุทธิ</span>
                <span class="text-orange-600">฿{{ totalAmount }}</span>
            </div>
            <button @click="submitOrder" class="btn bg-orange-600 hover:bg-orange-700 text-white w-full rounded-xl border-none shadow-lg text-lg">
                ยืนยันการสั่งอาหาร
            </button>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-black/40"><button>close</button></form>
    </dialog>

    <dialog id="history_modal" class="modal modal-bottom sm:modal-middle">
      <div class="modal-box bg-white p-0 rounded-t-2xl sm:rounded-2xl">

        <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
           <h3 class="font-bold text-lg flex items-center gap-2 text-gray-800">
             <span class="text-xl">🧾</span> ประวัติการสั่ง
           </h3>
           <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost text-gray-400">✕</button></form>
        </div>

        <div class="p-4 bg-white max-h-[70vh] overflow-y-auto min-h-[200px]">
            <div v-if="orderHistory.length === 0" class="flex flex-col items-center justify-center h-40 text-gray-400">
                <span class="text-4xl mb-2">🍽️</span>
                <p>ยังไม่มีรายการที่สั่ง</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="order in orderHistory" :key="order.id" class="border border-gray-200 rounded-xl p-4 bg-white shadow-sm">
                    <div class="flex justify-between items-start mb-3 pb-2 border-b border-dashed border-gray-200">
                        <div>
                            <div class="text-xs font-mono text-gray-400">Invoice: #{{ order.invoice_number.substring(0,8) }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                {{ new Date(order.created_at).toLocaleTimeString('th-TH', {hour: '2-digit', minute:'2-digit'}) }} น.
                            </div>
                        </div>
                        <div :class="['badge text-white border-none py-3 px-3', getStatusColor(order.status)]">
                            {{ getStatusText(order.status) }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div v-for="item in order.items" :key="item.id" class="flex justify-between text-sm items-center">
                            <span class="text-gray-700 font-medium">{{ item.product_name }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400">x{{ item.quantity }}</span>
                                <span class="text-gray-800 font-bold">฿{{ item.subtotal }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-sm text-gray-500">รวมทั้งสิ้น</span>
                        <span class="text-lg font-bold text-orange-600">฿{{ order.total_amount }}</span>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-black/40"><button>close</button></form>
    </dialog>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// --- Data ---
const tableId = ref(null); // ค่าเริ่มต้นเป็น null แล้วค่อยดึงจาก URL
const products = ref([]);
const categories = ref([]);
const cart = ref([]);
const orderHistory = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const selectedCategory = ref(0); // 0 = All

// --- Fetch Data ---
const fetchData = async () => {
    try {
        const [prodRes, catRes] = await Promise.all([
            axios.get('/api/products'),
            axios.get('/api/categories')
        ]);
        products.value = Array.isArray(prodRes.data) ? prodRes.data : prodRes.data.data;
        categories.value = catRes.data;
    } catch (e) { console.error(e); }
    finally { loading.value = false; }
}

const fetchHistory = async () => {
    if (!tableId.value) return; // ถ้าไม่มี tableId ไม่ต้องดึง
    try {
        const res = await axios.get(`/api/orders/table/${tableId.value}`);
        orderHistory.value = res.data;
    } catch (e) { console.error(e); }
}

// --- Computed ---
const filteredProducts = computed(() => {
    let result = products.value;

    // Filter by Category
    if (selectedCategory.value !== 0) {
        result = result.filter(p => p.category_id === selectedCategory.value);
    }

    // Filter by Search
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(p => p.name.toLowerCase().includes(query));
    }

    return result;
});

const totalAmount = computed(() => cart.value.reduce((sum, i) => sum + (i.price * i.qty), 0));
const cartItemCount = computed(() => cart.value.reduce((sum, i) => sum + i.qty, 0));

// --- Actions ---
const addToCart = (product) => {
    const item = cart.value.find(i => i.id === product.id);
    if (item) item.qty++;
    else cart.value.push({ ...product, qty: 1 });

    // สั่นมือถือเบาๆ เมื่อกดใส่ตะกร้า (ถ้า Browser รองรับ)
    if (navigator.vibrate) navigator.vibrate(50);
};

const increaseQty = (index) => cart.value[index].qty++;
const decreaseQty = (index) => {
    if(cart.value[index].qty > 1) cart.value[index].qty--;
    else cart.value.splice(index, 1);
};

const openCartModal = () => document.getElementById('cart_modal').showModal();
const openHistoryModal = () => {
    fetchHistory();
    document.getElementById('history_modal').showModal();
}

const submitOrder = async () => {
    if (!tableId.value) {
        alert('ไม่พบข้อมูลโต๊ะ (Table ID) กรุณาสแกน QR Code ใหม่');
        return;
    }

    try {
        await axios.post('/api/orders', {
            table_id: tableId.value,
            items: cart.value.map(i => ({ id: i.id, qty: i.qty }))
        });
        document.getElementById('cart_modal').close();

        // Clear cart & Show History
        cart.value = [];
        // alert('✅ ส่งออเดอร์เรียบร้อย!');
        openHistoryModal(); // เปิดหน้าติดตามสถานะทันที
    } catch (e) {
        alert('เกิดข้อผิดพลาดในการสั่งอาหาร กรุณาลองใหม่');
    }
};

// --- Helpers ---
const getStatusText = (status) => {
    const map = {
        pending: 'รอคิว',
        cooking: 'กำลังปรุง 🔥',
        ready: 'รอเสิร์ฟ 🛎️',
        served: 'เสิร์ฟแล้ว ✅',
        paid: 'ชำระแล้ว 💰'
    };
    return map[status] || status;
}
const getStatusColor = (status) => {
    const map = {
        pending: 'bg-gray-400',
        cooking: 'bg-orange-500 animate-pulse',
        ready: 'bg-green-500',
        served: 'bg-blue-500'
    };
    return map[status] || 'bg-gray-400';
}

// --- Lifecycle ---
onMounted(() => {
    // ดึง Table ID จาก URL (เช่น /table/5)
    const pathSegments = window.location.pathname.split('/');
    if (pathSegments.includes('table')) {
        const idIndex = pathSegments.indexOf('table') + 1;
        if (pathSegments[idIndex]) {
            tableId.value = pathSegments[idIndex];
        }
    } else {
        // กรณีเทสในหน้าอื่น ให้ใส่ค่า Default ไว้เทส
        // tableId.value = 1;
    }

    fetchData();
});
</script>

<style scoped>
/* ซ่อน Scrollbar แนวนอนแต่ยังเลื่อนได้ */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
