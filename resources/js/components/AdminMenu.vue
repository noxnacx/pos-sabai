<template>
  <!-- ใช้ bg-slate-50 และปรับ Layout ให้รองรับ Sidebar -->
  <div class="min-h-screen bg-slate-50 font-sans">

    <!-- 🟢 1. เรียกใช้ Sidebar ตรงนี้ -->
    <admin-sidebar></admin-sidebar>

    <!-- 🟡 2. ส่วนเนื้อหาหลัก (ขยับขวา 64 หน่วย เพื่อหลบ Sidebar) -->
    <div class="lg:pl-64 transition-all duration-300">

        <!-- Header (White Bar) -->
        <div class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">จัดการเมนูอาหาร</h1>

                    <button
                        @click="openModal()"
                        class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-none gap-2 shadow-md hover:shadow-lg transition-all"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        เพิ่มเมนูใหม่
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="max-w-7xl mx-auto p-6 pb-20">

          <!-- ตารางแสดงสินค้า (Card) -->
          <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <span class="text-sm font-medium text-slate-500">รายการทั้งหมด {{ products.length }} เมนู</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                  <!-- หัวตาราง -->
                  <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold tracking-wider">
                    <tr>
                      <th class="py-4 pl-6 w-20">รูปภาพ</th>
                      <th>ชื่อเมนู / รหัส</th>
                      <th>หมวดหมู่</th>
                      <th>ราคา</th>
                      <th class="text-center">สถานะการขาย</th>
                      <th class="text-center w-32">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="product in products" :key="product.id" class="hover:bg-indigo-50/30 transition-colors group">

                      <!-- รูป -->
                      <td class="pl-6 py-3">
                        <div class="avatar">
                          <div class="w-14 h-14 rounded-lg shadow-sm border border-slate-100 bg-white p-0.5">
                            <img
                              :src="product.image_url || 'https://via.placeholder.com/150?text=No+Img'"
                              class="rounded-md object-cover w-full h-full"
                            />
                          </div>
                        </div>
                      </td>

                      <!-- ชื่อ -->
                      <td>
                        <div class="font-bold text-slate-800 text-base group-hover:text-indigo-600 transition-colors">
                            {{ product.name }}
                        </div>
                        <div class="text-xs text-slate-400 font-mono mt-0.5">
                            SKU: {{ product.id.toString().padStart(4, '0') }}
                        </div>
                      </td>

                      <!-- หมวดหมู่ -->
                      <td>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                            {{ product.category?.name || 'ไม่มีหมวด' }}
                        </span>
                      </td>

                      <!-- ราคา -->
                      <td>
                        <div class="font-bold text-slate-700">
                            ฿{{ product.price }}
                        </div>
                      </td>

                      <!-- สถานะ (Switch) -->
                      <td class="text-center">
                         <div class="flex flex-col items-center justify-center">
                             <input
                                type="checkbox"
                                class="toggle toggle-success toggle-sm"
                                :checked="product.is_active"
                                @change="toggleStatus(product)"
                             />
                             <span :class="['text-[10px] mt-1 font-medium', product.is_active ? 'text-emerald-600' : 'text-slate-400']">
                                {{ product.is_active ? 'พร้อมขาย' : 'ของหมด' }}
                             </span>
                         </div>
                      </td>

                      <!-- ปุ่มจัดการ -->
                      <td class="text-center">
                        <div class="flex justify-center gap-2">
                            <button
                                @click="openModal(product)"
                                class="btn btn-sm btn-square btn-ghost text-amber-500 hover:bg-amber-50 hover:border-amber-200 border border-transparent"
                                title="แก้ไข"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                            <button
                                @click="deleteProduct(product.id)"
                                class="btn btn-sm btn-square btn-ghost text-rose-500 hover:bg-rose-50 hover:border-rose-200 border border-transparent"
                                title="ลบ"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </div>

            <div v-if="products.length === 0" class="py-16 text-center bg-slate-50/50">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-200 mb-4 text-3xl">🍽️</div>
                <h3 class="text-lg font-bold text-slate-700">ยังไม่มีรายการอาหาร</h3>
                <p class="text-slate-500 mt-1">กดปุ่ม "เพิ่มเมนูใหม่" มุมขวาบนเพื่อเริ่มต้นใช้งาน</p>
            </div>
          </div>
        </div>
    </div>

    <!-- 🟦 Modal Form (ดีไซน์เดิม) -->
    <dialog class="modal backdrop-blur-sm" :class="{ 'modal-open': showModal }">
      <div class="modal-box w-11/12 max-w-lg bg-white p-0 overflow-hidden shadow-2xl rounded-2xl">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                <span class="text-2xl">{{ isEditing ? '✏️' : '✨' }}</span>
                {{ isEditing ? 'แก้ไขเมนูอาหาร' : 'สร้างเมนูใหม่' }}
            </h3>
            <button @click="closeModal" class="btn btn-sm btn-circle btn-ghost text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <div class="p-6 space-y-5">
          <form @submit.prevent="submitProduct">

            <!-- ชื่อเมนู -->
            <div class="form-control w-full">
              <label class="label pt-0 pb-1">
                  <span class="label-text font-semibold text-slate-600">ชื่อเมนูอาหาร <span class="text-red-500">*</span></span>
              </label>
              <input v-model="form.name" type="text" placeholder="เช่น ข้าวกะเพราหมูสับ" class="input input-bordered w-full bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- หมวดหมู่ -->
                <div class="form-control w-full">
                  <label class="label pb-1">
                      <span class="label-text font-semibold text-slate-600">หมวดหมู่ <span class="text-red-500">*</span></span>
                  </label>
                  <select v-model="form.category_id" class="select select-bordered w-full bg-white focus:border-indigo-500" required>
                    <option value="" disabled>เลือกหมวด...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                </div>

                <!-- ราคา -->
                <div class="form-control w-full">
                  <label class="label pb-1">
                      <span class="label-text font-semibold text-slate-600">ราคา (บาท) <span class="text-red-500">*</span></span>
                  </label>
                  <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-slate-400 font-bold">฿</span>
                      </div>
                      <input v-model="form.price" type="number" class="input input-bordered w-full pl-8 bg-white font-bold text-slate-700 focus:border-indigo-500" required />
                  </div>
                </div>
            </div>

            <!-- รูปภาพ -->
            <div class="form-control w-full">
              <label class="label pb-1">
                  <span class="label-text font-semibold text-slate-600">รูปภาพประกอบ</span>
              </label>
              <input @change="handleFileUpload" type="file" accept="image/*" class="file-input file-input-bordered file-input-primary w-full bg-white text-slate-500" />
              <div class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                  แนะนำไฟล์ .jpg หรือ .png ขนาดไม่เกิน 2MB
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex gap-3 justify-end">
              <button type="button" @click="closeModal" class="btn btn-ghost text-slate-500 hover:bg-slate-100">ยกเลิก</button>
              <button type="submit" class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-none px-6" :disabled="loading">
                <span v-if="loading" class="loading loading-spinner loading-xs"></span>
                {{ loading ? 'กำลังบันทึก...' : 'บันทึกข้อมูล' }}
              </button>
            </div>

          </form>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-slate-900/20">
        <button @click="closeModal">close</button>
      </form>
    </dialog>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminSidebar from './AdminSidebar.vue'; // 🟢 Import Sidebar เข้ามาใช้แบบ Local

const products = ref([]);
const categories = ref([]);
const showModal = ref(false);
const loading = ref(false);
const isEditing = ref(false);

const form = ref({
    id: null,
    name: '',
    price: '',
    category_id: '',
    image: null
});

// ... (ฟังก์ชัน fetchInitialData, submitProduct, deleteProduct, toggleStatus เหมือนเดิมทุกประการ) ...
const fetchInitialData = async () => {
    try {
        const [prodRes, catRes] = await Promise.all([
            axios.get('/api/products'),
            axios.get('/api/categories')
        ]);
        products.value = prodRes.data.data;
        categories.value = catRes.data;
    } catch(e) { console.error(e); }
}

const openModal = (product = null) => {
    if (product) {
        isEditing.value = true;
        form.value = { ...product, image: null };
    } else {
        isEditing.value = false;
        form.value = { name: '', price: '', category_id: '', image: null };
    }
    showModal.value = true;
}

const closeModal = () => { showModal.value = false; }
const handleFileUpload = (event) => { form.value.image = event.target.files[0]; }

const submitProduct = async () => {
    loading.value = true;
    try {
        const formData = new FormData();
        formData.append('name', form.value.name);
        formData.append('price', form.value.price);
        formData.append('category_id', form.value.category_id);
        if (form.value.image) formData.append('image', form.value.image);

        if (isEditing.value) await axios.post(`/api/products/${form.value.id}`, formData);
        else await axios.post('/api/products', formData);

        closeModal();
        fetchInitialData();
    } catch (e) { alert('เกิดข้อผิดพลาด'); console.error(e); }
    finally { loading.value = false; }
}

const deleteProduct = async (id) => {
    if(!confirm('ต้องการลบเมนูนี้ใช่ไหม?')) return;
    try { await axios.delete(`/api/products/${id}`); fetchInitialData(); } catch (e) { alert('ลบไม่สำเร็จ'); }
}

const toggleStatus = async (product) => {
    const originalStatus = product.is_active;
    product.is_active = !product.is_active;
    try { await axios.post(`/api/products/${product.id}`, { is_active: product.is_active }); }
    catch (e) { product.is_active = originalStatus; alert('แก้ไขสถานะไม่สำเร็จ'); }
}

onMounted(() => { fetchInitialData(); });
</script>
