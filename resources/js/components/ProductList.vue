<template>
  <div class="min-h-screen bg-gray-50 font-sans pb-32">

    <div v-if="isTableClosed" class="fixed inset-0 z-[100] bg-slate-100 flex flex-col items-center justify-center p-6 text-center animate-fade-in">
        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg mb-6">
            <span class="text-6xl">🔒</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800 mb-2">โต๊ะนี้ยังไม่เปิดให้บริการ</h1>
        <p class="text-slate-500 mb-8">กรุณาติดต่อพนักงานเพื่อเปิดโต๊ะก่อนสั่งอาหารครับ</p>
        <div class="text-sm text-slate-400">Table ID: {{ tableId }}</div>
    </div>

    <div v-else>
        <div class="bg-white/90 backdrop-blur-md sticky top-0 z-40 shadow-sm border-b border-gray-100">
          <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">

            <button @click="openHistoryModal" class="btn btn-circle btn-ghost btn-sm bg-gray-100 hover:bg-gray-200 transition-colors relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </button>

            <div class="text-center">
              <h1 class="text-lg font-bold text-gray-800 tracking-tight">ร้านอาหารไทย</h1>
              <p class="text-xs text-gray-400 font-medium">โต๊ะ {{ tableName || 'Loading...' }}</p>
            </div>

            <div class="relative">
              <button @click="openCartModal" class="btn btn-circle btn-ghost btn-sm bg-orange-50 text-orange-500 hover:bg-orange-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </button>
              <div v-if="cartItemCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full animate-bounce shadow-sm border-2 border-white">
                {{ cartItemCount }}
              </div>
            </div>
          </div>
        </div>

        <div class="max-w-7xl mx-auto">

          <div class="px-4 py-6">
            <div class="relative group">
              <span class="absolute inset-y-0 left-0 flex items-center pl-4 transition-colors group-focus-within:text-orange-500">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </span>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="ค้นหาเมนูอร่อยๆ..."
                class="w-full py-3 pl-12 pr-4 text-gray-700 bg-white border border-gray-200 rounded-2xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition-all placeholder-gray-300"
              >
            </div>
          </div>

          <div class="px-4 pb-6">
            <div class="flex overflow-x-auto gap-3 pb-2 scrollbar-hide snap-x">
              <button
                @click="selectedCategory = 0"
                :class="['px-5 py-2 rounded-full whitespace-nowrap text-sm font-bold transition-all duration-300 snap-start border', selectedCategory === 0 ? 'bg-orange-500 border-orange-500 text-white shadow-lg shadow-orange-200 scale-105' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50']"
              >
                🔥 ทั้งหมด
              </button>
              <button
                v-for="cat in categories"
                :key="cat.id"
                @click="selectedCategory = cat.id"
                :class="['px-5 py-2 rounded-full whitespace-nowrap text-sm font-bold transition-all duration-300 snap-start border', selectedCategory === cat.id ? 'bg-orange-500 border-orange-500 text-white shadow-lg shadow-orange-200 scale-105' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50']"
              >
                {{ cat.name }}
              </button>
            </div>
          </div>

          <div class="px-4 min-h-[50vh]">

            <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              <div v-for="n in 4" :key="n" class="bg-white p-3 rounded-2xl border border-gray-100 h-32 sm:h-72 animate-pulse flex sm:flex-col gap-4">
                 <div class="w-28 sm:w-full h-28 sm:h-40 bg-gray-200 rounded-xl"></div>
                 <div class="flex-1 space-y-2 py-2">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                 </div>
              </div>
            </div>

            <div v-else-if="filteredProducts.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
              <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-3xl">🍲</div>
              <p class="font-medium">ไม่พบเมนูที่ค้นหา</p>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
              <div
                v-for="product in filteredProducts"
                :key="product.id"
                class="group bg-white p-3 sm:p-4 rounded-2xl border border-gray-100 hover:border-orange-100 hover:shadow-xl hover:shadow-orange-50 transition-all duration-300 flex flex-row sm:flex-col gap-4 relative overflow-hidden"
              >
                <div class="w-28 sm:w-full h-28 sm:h-48 flex-shrink-0 relative rounded-xl overflow-hidden bg-gray-100">
                  <img
                    :src="product.image_url || 'https://via.placeholder.com/300?text=No+Image'"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                    :class="{ 'grayscale opacity-70': !product.is_active }"
                  >
                  <div v-if="!product.is_active" class="absolute inset-0 bg-black/50 flex items-center justify-center text-white font-bold text-sm backdrop-blur-sm">
                      หมดชั่วคราว
                  </div>
                </div>

                <div class="flex-1 flex flex-col justify-between sm:pt-2">
                  <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-800 line-clamp-1 group-hover:text-orange-600 transition-colors">
                      {{ product.name }}
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-400 mt-1 line-clamp-2 leading-relaxed">
                      {{ product.description || 'เมนูแนะนำ รสชาติอร่อย วัตถุดิบคุณภาพ' }}
                    </p>
                  </div>

                  <div class="flex justify-between items-center mt-3 sm:mt-4">
                    <span class="text-lg sm:text-xl font-bold text-orange-600">
                      ฿{{ product.price }}
                    </span>

                    <button
                      v-if="product.is_active"
                      @click="addToCart(product)"
                      class="btn btn-circle btn-sm sm:btn-md bg-orange-500 hover:bg-orange-600 border-none text-white shadow-lg shadow-orange-200 active:scale-90 transition-transform"
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
        </div>

        <div v-if="cart.length > 0" class="fixed bottom-0 sm:bottom-6 left-0 right-0 px-0 sm:px-4 z-50 flex justify-center pointer-events-none">
          <div class="w-full sm:max-w-xl bg-white/95 backdrop-blur-md sm:rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border-t sm:border border-gray-100 p-4 pointer-events-auto transform transition-all animate-slide-up">
            <div class="flex items-center justify-between gap-4">
              <div class="flex flex-col">
                <span class="text-xs text-gray-500 font-medium">รวมทั้งหมด {{ cartItemCount }} รายการ</span>
                <span class="text-2xl font-black text-gray-800 tracking-tight">฿{{ totalAmount }}</span>
              </div>
              <button
                @click="openCartModal"
                class="btn bg-orange-600 hover:bg-orange-700 text-white border-none rounded-xl px-8 shadow-lg shadow-orange-300 flex items-center gap-2 h-12"
              >
                ดูตะกร้า
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" /></svg>
              </button>
            </div>
          </div>
        </div>

        <dialog id="cart_modal" class="modal modal-bottom sm:modal-middle">
          <div class="modal-box bg-white p-0 rounded-t-2xl sm:rounded-2xl max-w-lg">
            <div class="bg-gray-50 px-5 py-4 border-b flex justify-between items-center sticky top-0 z-10">
               <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">🛒 ตะกร้าของฉัน</h3>
               <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost text-gray-400 hover:bg-gray-200">✕</button></form>
            </div>

            <div class="p-5 bg-white max-h-[60vh] overflow-y-auto">
                <div v-if="cart.length === 0" class="text-center py-12 text-gray-400">ยังไม่มีสินค้าในตะกร้า</div>

                <div v-for="(item, index) in cart" :key="index" class="flex justify-between items-center border-b border-gray-100 py-4 last:border-0">
                    <div class="flex gap-4 items-center">
                       <div class="w-16 h-16 rounded-xl bg-gray-100 overflow-hidden flex-shrink-0 border border-gray-200">
                          <img :src="item.image_url" class="w-full h-full object-cover">
                       </div>
                       <div>
                           <div class="font-bold text-gray-800 text-base line-clamp-1">{{ item.name }}</div>
                           <div class="text-sm text-gray-500">฿{{ item.price }} / ชิ้น</div>
                       </div>
                    </div>

                    <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-1 border border-gray-200">
                        <button @click="decreaseQty(index)" class="w-8 h-8 flex items-center justify-center bg-white rounded shadow-sm text-gray-600 hover:text-red-500 active:scale-90 transition-transform font-bold text-lg">-</button>
                        <span class="font-bold text-base w-4 text-center">{{ item.qty }}</span>
                        <button @click="increaseQty(index)" class="w-8 h-8 flex items-center justify-center bg-white rounded shadow-sm text-green-600 hover:text-green-700 active:scale-90 transition-transform font-bold text-lg">+</button>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-gray-50 border-t" v-if="cart.length > 0">
                <div class="flex justify-between text-xl font-bold mb-5 text-gray-800">
                    <span>รวมสุทธิ</span>
                    <span class="text-orange-600">฿{{ totalAmount }}</span>
                </div>
                <button @click="submitOrder" class="btn bg-orange-600 hover:bg-orange-700 text-white w-full rounded-xl border-none shadow-lg shadow-orange-200 text-lg h-12">
                    ยืนยันการสั่งอาหาร
                </button>
            </div>
          </div>
          <form method="dialog" class="modal-backdrop bg-black/40 backdrop-blur-sm"><button>close</button></form>
        </dialog>

        <dialog id="history_modal" class="modal modal-bottom sm:modal-middle">
          <div class="modal-box bg-white p-0 rounded-t-2xl sm:rounded-2xl max-w-lg">
            <div class="bg-gray-50 px-5 py-4 border-b flex justify-between items-center sticky top-0 z-10">
               <h3 class="font-bold text-lg flex items-center gap-2 text-gray-800">
                 <span>🧾</span> ประวัติการสั่ง
               </h3>
               <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost text-gray-400 hover:bg-gray-200">✕</button></form>
            </div>

            <div class="p-5 bg-white max-h-[70vh] overflow-y-auto">
                <div v-if="orderHistory.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400 opacity-60">
                    <span class="text-5xl mb-3">🍽️</span>
                    <p>ยังไม่มีรายการที่สั่ง</p>
                </div>

                <div v-else class="space-y-5">
                    <div v-for="order in orderHistory" :key="order.id" class="border border-gray-200 rounded-2xl p-5 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-4 pb-3 border-b border-dashed border-gray-200">
                            <div>
                                <div class="text-xs font-mono text-gray-400 bg-gray-100 px-2 py-1 rounded inline-block">#{{ order.invoice_number.substring(0,8) }}</div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>
                                    {{ new Date(order.created_at).toLocaleTimeString('th-TH', {hour: '2-digit', minute:'2-digit'}) }} น.
                                </div>
                            </div>
                            <div :class="['badge text-white border-none py-3 px-3 font-bold', getStatusColor(order.status)]">
                                {{ getStatusText(order.status) }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div v-for="item in order.items" :key="item.id" class="flex justify-between text-sm items-center bg-slate-50 p-2 rounded-lg">
                                <span class="text-gray-700 font-medium flex-1">{{ item.product_name }}</span>

                                <div v-if="order.status === 'pending'" class="flex items-center gap-3 bg-white border border-gray-200 rounded px-1 mx-2">
                                    <button
                                        @click="updateItemQty(item, item.quantity - 1)"
                                        class="text-red-500 font-bold px-2 hover:bg-red-50 rounded"
                                        :class="{'opacity-50 cursor-not-allowed': loading}"
                                    >
                                        {{ item.quantity === 1 ? '🗑️' : '-' }}
                                    </button>
                                    <span class="font-bold text-gray-800 w-4 text-center">{{ item.quantity }}</span>
                                    <button
                                        @click="updateItemQty(item, item.quantity + 1)"
                                        class="text-green-600 font-bold px-2 hover:bg-green-50 rounded"
                                        :class="{'opacity-50 cursor-not-allowed': loading}"
                                    >
                                        +
                                    </button>
                                </div>

                                <div v-else class="flex items-center gap-2 mx-2">
                                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">x{{ item.quantity }}</span>
                                </div>

                                <div class="text-gray-800 font-bold">฿{{ item.subtotal }}</div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-sm text-gray-500 font-medium">รวมทั้งสิ้น</span>
                            <span class="text-xl font-bold text-orange-600">฿{{ order.total_amount }}</span>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          <form method="dialog" class="modal-backdrop bg-black/40 backdrop-blur-sm"><button>close</button></form>
        </dialog>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// --- Data ---
const tableId = ref(null);
const tableName = ref('');
const isTableClosed = ref(true); // 🔒 เริ่มต้นให้ปิดไว้ก่อน
const products = ref([]);
const categories = ref([]);
const cart = ref([]);
const orderHistory = ref([]);
const loading = ref(true);
const searchQuery = ref('');
const selectedCategory = ref(0); // 0 = All

// --- Fetch Data ---
const checkTableStatus = async () => {
    if (!tableId.value) return;
    try {
        const res = await axios.get(`/api/tables/${tableId.value}`);
        if (res.data.status === 'occupied') {
            isTableClosed.value = false; // ✅ เปิดให้เข้า
            tableName.value = res.data.name;
            fetchData(); // โหลดข้อมูลอาหาร
        } else {
            isTableClosed.value = true; // 🚫 ปิด
        }
    } catch (e) {
        console.error("Error checking table status:", e);
        isTableClosed.value = true; // กันไว้ก่อน
    }
}

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
    if (!tableId.value) return;
    try {
        const res = await axios.get(`/api/orders/table/${tableId.value}`);
        orderHistory.value = res.data;
    } catch (e) { console.error(e); }
}

// --- Computed ---
const filteredProducts = computed(() => {
    let result = products.value;
    if (selectedCategory.value !== 0) {
        result = result.filter(p => p.category_id === selectedCategory.value);
    }
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
    if (navigator.vibrate) navigator.vibrate(30);
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
        alert('กรุณาสแกน QR Code เพื่อระบุโต๊ะ');
        return;
    }
    try {
        await axios.post('/api/orders', {
            table_id: tableId.value,
            items: cart.value.map(i => ({ id: i.id, qty: i.qty }))
        });
        document.getElementById('cart_modal').close();
        cart.value = [];
        openHistoryModal();
    } catch (e) { alert('Error sending order'); }
};

// ✨ ฟังก์ชันแก้ไขจำนวนสินค้าในออเดอร์ (เพิ่มใหม่)
const updateItemQty = async (item, newQty) => {
    if (newQty === 0 && !confirm(`ต้องการยกเลิกเมนู "${item.product_name}" ใช่ไหม?`)) return;

    try {
        // เรียก API ที่เราทำไว้ใน Backend
        await axios.post(`/api/order-items/${item.id}/update`, { quantity: newQty });
        fetchHistory(); // โหลดประวัติใหม่เพื่ออัปเดตยอดเงิน
    } catch (e) {
        alert(e.response?.data?.message || 'แก้ไขไม่ได้ (ออเดอร์อาจถูกรับไปแล้ว)');
        fetchHistory(); // รีเฟรชสถานะให้ตรงความเป็นจริง
    }
}

// --- Helpers ---
const getStatusText = (status) => {
    const map = { pending: 'รอคิว 🕒', cooking: 'กำลังปรุง 🔥', ready: 'รอเสิร์ฟ 🛎️', served: 'เสิร์ฟแล้ว ✅', paid: 'ชำระแล้ว 💰' };
    return map[status] || status;
}
const getStatusColor = (status) => {
    const map = { pending: 'bg-gray-400', cooking: 'bg-orange-500 animate-pulse', ready: 'bg-green-500', served: 'bg-blue-500' };
    return map[status] || 'bg-gray-400';
}

// --- Lifecycle ---
onMounted(() => {
    const pathSegments = window.location.pathname.split('/');
    if (pathSegments.includes('table')) {
        const idIndex = pathSegments.indexOf('table') + 1;
        if (pathSegments[idIndex]) {
            tableId.value = pathSegments[idIndex];
            checkTableStatus();
        }
    }
});
</script>

<style scoped>
/* ซ่อน scrollbar แนวนอน */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

@keyframes slide-up {
  from { transform: translateY(100%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-up {
  animation: slide-up 0.3s ease-out forwards;
}

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}
</style>
