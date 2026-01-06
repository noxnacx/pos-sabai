<template>
  <div class="min-h-screen bg-slate-50 font-sans">

    <admin-sidebar></admin-sidebar>

    <div class="lg:pl-64 transition-all duration-300">

        <div class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">จัดการเมนูอาหาร</h1>

                    <div class="flex gap-2">
                        <button
                            @click="openCategoryManager"
                            class="btn btn-outline border-slate-300 text-slate-600 hover:bg-slate-50 hover:text-indigo-600 gap-2 shadow-sm"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            จัดการหมวดหมู่
                        </button>

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
        </div>

        <div class="max-w-7xl mx-auto p-6 pb-20">

          <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="relative w-full md:w-1/3">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input v-model="filters.search" type="text" placeholder="ค้นหาชื่อเมนู..." class="input input-bordered w-full pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-indigo-500 transition-all" />
            </div>

            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                <select v-model="filters.category_id" class="select select-bordered bg-slate-50 border-slate-200 w-full md:w-48">
                    <option value="">📂 ทุกหมวดหมู่</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <select v-model="filters.status" class="select select-bordered bg-slate-50 border-slate-200 w-full md:w-40">
                    <option value="">⚡ ทุกสถานะ</option>
                    <option value="active">🟢 เปิดขาย</option>
                    <option value="inactive">🔴 ปิด</option>
                </select>
                <button @click="resetFilters" class="btn btn-square btn-ghost text-slate-400 hover:text-slate-600 border border-slate-200" title="ล้างตัวกรอง">↺</button>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden min-h-[400px] flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <span class="text-sm font-medium text-slate-500">
                    แสดง {{ paginatedProducts.length }} จากทั้งหมด {{ filteredProducts.length }} รายการ
                </span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="table w-full">
                  <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-bold tracking-wider">
                    <tr>
                      <th class="py-4 pl-6 w-20">รูปภาพ</th>
                      <th>ชื่อเมนู / รายละเอียด</th>
                      <th>หมวดหมู่</th>
                      <th>ราคา</th>
                      <th class="text-center">สถานะ</th>
                      <th class="text-center w-32">จัดการ</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-indigo-50/30 transition-colors group">
                      <td class="pl-6 py-3">
                        <div class="avatar">
                          <div class="w-14 h-14 rounded-lg shadow-sm border border-slate-100 bg-white p-0.5">
                            <img :src="product.image_url || 'https://via.placeholder.com/150?text=No+Img'" class="rounded-md object-cover w-full h-full" />
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="font-bold text-slate-800 text-base group-hover:text-indigo-600 transition-colors">{{ product.name }}</div>
                        <div class="text-xs text-slate-400 font-mono mt-0.5 line-clamp-1">{{ product.description || '-' }}</div>
                      </td>
                      <td>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                            {{ product.category?.name || 'ไม่มีหมวด' }}
                        </span>
                      </td>
                      <td><div class="font-bold text-slate-700">฿{{ product.price }}</div></td>
                      <td class="text-center">
                         <div class="flex flex-col items-center justify-center">
                             <input type="checkbox" class="toggle toggle-success toggle-sm" :checked="product.is_active" @change="toggleStatus(product)" />
                             <span :class="['text-[10px] mt-1 font-bold', product.is_active ? 'text-emerald-600' : 'text-slate-400']">{{ product.is_active ? 'เปิดขาย' : 'ปิด' }}</span>
                         </div>
                      </td>
                      <td class="text-center">
                        <div class="flex justify-center gap-2">
                            <button @click="openModal(product)" class="btn btn-sm btn-square btn-ghost text-amber-500 hover:bg-amber-50 hover:border-amber-200 border border-transparent"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                            <button @click="deleteProduct(product.id)" class="btn btn-sm btn-square btn-ghost text-rose-500 hover:bg-rose-50 hover:border-rose-200 border border-transparent"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
            </div>

            <div v-if="filteredProducts.length > itemsPerPage" class="bg-white px-6 py-4 border-t border-slate-100 flex justify-center items-center gap-2">
                <button @click="currentPage--" :disabled="currentPage === 1" class="btn btn-sm btn-outline border-slate-300 text-slate-600 disabled:bg-slate-50 disabled:text-slate-300">« ก่อนหน้า</button>
                <div class="join">
                    <button v-for="page in totalPages" :key="page" @click="currentPage = page" :class="['join-item btn btn-sm', currentPage === page ? 'btn-active btn-primary text-white' : 'btn-ghost bg-slate-50 text-slate-600 border-slate-200']">{{ page }}</button>
                </div>
                <button @click="currentPage++" :disabled="currentPage === totalPages" class="btn btn-sm btn-outline border-slate-300 text-slate-600 disabled:bg-slate-50 disabled:text-slate-300">ถัดไป »</button>
            </div>
          </div>
        </div>
    </div>

    <dialog class="modal backdrop-blur-sm" :class="{ 'modal-open': showModal }">
      <div class="modal-box w-11/12 max-w-xl bg-white p-0 overflow-hidden shadow-2xl rounded-2xl border border-slate-100">
        <div class="bg-slate-50 px-8 py-5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-xl text-slate-800 flex items-center gap-3">
                <span class="text-3xl">{{ isEditing ? '✏️' : '✨' }}</span> {{ isEditing ? 'แก้ไขเมนูอาหาร' : 'สร้างเมนูใหม่' }}
            </h3>
            <button @click="closeModal" class="btn btn-sm btn-circle btn-ghost text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <div class="p-8 space-y-6">
          <form @submit.prevent="submitProduct">
            <div class="form-control w-full">
              <label class="label pt-0 pb-1"><span class="label-text font-bold text-base text-slate-700">ชื่อเมนูอาหาร <span class="text-red-500">*</span></span></label>
              <input v-model="form.name" type="text" class="input input-lg input-bordered w-full bg-gray-50 border-gray-300 focus:bg-white focus:border-indigo-500 transition-all text-lg" required />
            </div>
            <div class="form-control w-full">
              <label class="label pt-0 pb-1"><span class="label-text font-bold text-base text-slate-700">รายละเอียด</span></label>
              <textarea v-model="form.description" class="textarea textarea-bordered h-20 bg-gray-50 border-gray-300 focus:bg-white focus:border-indigo-500 transition-all text-base leading-relaxed"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-control w-full">
                  <label class="label pt-0 pb-1"><span class="label-text font-bold text-base text-slate-700">หมวดหมู่ <span class="text-red-500">*</span></span></label>
                  <select v-model="form.category_id" class="select select-lg select-bordered w-full bg-gray-50 border-gray-300 focus:bg-white focus:border-indigo-500 transition-all text-base" required>
                    <option value="" disabled>เลือกหมวด...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                </div>
                <div class="form-control w-full">
                  <label class="label pt-0 pb-1"><span class="label-text font-bold text-base text-slate-700">ราคา <span class="text-red-500">*</span></span></label>
                  <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><span class="text-slate-400 font-bold text-lg">฿</span></div>
                      <input v-model="form.price" type="number" class="input input-lg input-bordered w-full pl-10 bg-gray-50 border-gray-300 font-bold text-slate-700 focus:bg-white focus:border-indigo-500 transition-all text-lg" required />
                  </div>
                </div>
            </div>
            <div class="form-control w-full">
              <label class="label pt-0 pb-1"><span class="label-text font-bold text-base text-slate-700">รูปภาพประกอบ</span></label>
              <input @change="handleFileUpload" type="file" accept="image/*" class="file-input file-input-lg file-input-bordered file-input-primary w-full bg-gray-50 border-gray-300 text-slate-500" />
            </div>
            <div class="mt-8 flex gap-4 justify-end pt-6 border-t border-slate-100">
              <button type="button" @click="closeModal" class="btn btn-lg btn-ghost text-slate-500 hover:bg-slate-100 px-8">ยกเลิก</button>
              <button type="submit" class="btn btn-lg bg-indigo-600 hover:bg-indigo-700 text-white border-none px-10 shadow-lg hover:shadow-indigo-200" :disabled="loading">
                <span v-if="loading" class="loading loading-spinner loading-sm"></span> {{ loading ? 'กำลังบันทึก...' : 'บันทึกข้อมูล' }}
              </button>
            </div>
          </form>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-slate-900/30"><button @click="closeModal">close</button></form>
    </dialog>

    <dialog id="category_modal" class="modal backdrop-blur-sm">
      <div class="modal-box w-11/12 max-w-md bg-white p-0 rounded-2xl shadow-xl">
        <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100 flex justify-between items-center">
             <h3 class="font-bold text-lg text-indigo-900">📂 จัดการหมวดหมู่</h3>
             <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost text-indigo-400 hover:bg-indigo-100">✕</button></form>
        </div>

        <div class="p-6">
            <div class="flex gap-2 mb-6">
                <input v-model="newCategoryName" type="text" placeholder="ชื่อหมวดหมู่ใหม่..." class="input input-bordered w-full bg-white focus:border-indigo-500" @keyup.enter="addNewCategoryManual" />
                <button @click="addNewCategoryManual" class="btn bg-indigo-600 hover:bg-indigo-700 text-white border-none shadow-md">
                   เพิ่ม
                </button>
            </div>

            <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1">
                <div v-if="categories.length === 0" class="text-center text-slate-400 py-4">ยังไม่มีหมวดหมู่</div>

                <div v-for="(cat, index) in categories" :key="cat.id" class="flex justify-between items-center p-2 pl-3 bg-slate-50 rounded-lg border border-slate-100 group hover:border-indigo-200 transition-colors">
                    <span class="font-medium text-slate-700 truncate w-full">{{ cat.name }}</span>

                    <div class="flex items-center gap-1">
                        <div class="flex flex-col gap-0.5">
                            <button
                                @click="moveCategory(index, 'up')"
                                :disabled="index === 0"
                                class="btn btn-xs btn-square btn-ghost text-slate-400 hover:text-indigo-600 disabled:opacity-20 h-5 min-h-0"
                            >▲</button>
                            <button
                                @click="moveCategory(index, 'down')"
                                :disabled="index === categories.length - 1"
                                class="btn btn-xs btn-square btn-ghost text-slate-400 hover:text-indigo-600 disabled:opacity-20 h-5 min-h-0"
                            >▼</button>
                        </div>

                        <div class="w-px h-6 bg-slate-200 mx-1"></div>

                        <button @click="deleteCategory(cat.id)" class="btn btn-xs btn-square btn-ghost text-slate-400 hover:text-red-500 hover:bg-red-50" title="ลบหมวดหมู่">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <form method="dialog" class="modal-backdrop bg-slate-900/30"><button>close</button></form>
    </dialog>

  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import AdminSidebar from './AdminSidebar.vue';

const products = ref([]);
const categories = ref([]);
const showModal = ref(false);
const loading = ref(false);
const isEditing = ref(false);
const newCategoryName = ref('');

const filters = ref({ search: '', category_id: '', status: '' });
const currentPage = ref(1);
const itemsPerPage = 10;

const form = ref({ id: null, name: '', description: '', price: '', category_id: '', image: null });

// ✨ Logic กรองและเรียง (เหมือนเดิม แต่ใช้ sort_order ที่ backend ส่งมา)
const filteredProducts = computed(() => {
    let result = products.value.filter(product => {
        const matchSearch = product.name.toLowerCase().includes(filters.value.search.toLowerCase());
        const matchCategory = filters.value.category_id ? product.category_id == filters.value.category_id : true;
        const matchStatus = filters.value.status === '' ? true : (filters.value.status === 'active' ? product.is_active : !product.is_active);
        return matchSearch && matchCategory && matchStatus;
    });

    // เรียงตาม Category ID ก่อน (ซึ่ง Category ถูกเรียงมาแล้วจาก Backend)
    // แต่เพื่อให้ชัวร์ เราจะ map index จาก categories array
    const catOrder = new Map(categories.value.map((c, i) => [c.id, i]));

    return result.sort((a, b) => {
        const orderA = catOrder.get(a.category_id) ?? 9999;
        const orderB = catOrder.get(b.category_id) ?? 9999;
        if (orderA !== orderB) return orderA - orderB;
        return a.name.localeCompare(b.name);
    });
});

const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    return filteredProducts.value.slice(start, start + itemsPerPage);
});

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage));

watch(filters, () => { currentPage.value = 1; }, { deep: true });
const resetFilters = () => { filters.value = { search: '', category_id: '', status: '' }; }

const fetchInitialData = async () => {
    try {
        const [prodRes, catRes] = await Promise.all([axios.get('/api/products'), axios.get('/api/categories')]);
        products.value = prodRes.data.data;
        categories.value = catRes.data; // มาแบบเรียงแล้วจาก Backend
    } catch(e) { console.error(e); }
}

// --- Category Management ---
const openCategoryManager = () => document.getElementById('category_modal').showModal();

const addNewCategoryManual = async () => {
    if (!newCategoryName.value.trim()) return;
    try {
        const res = await axios.post('/api/admin/categories', { name: newCategoryName.value });
        categories.value.push(res.data);
        newCategoryName.value = '';
        saveCategoryOrder(); // บันทึกให้ชัวร์
    } catch (e) { alert('เพิ่มหมวดหมู่ไม่สำเร็จ'); }
}

const deleteCategory = async (id) => {
    if(!confirm('ต้องการลบหมวดหมู่นี้?')) return;
    try {
        await axios.delete(`/api/admin/categories/${id}`);
        categories.value = categories.value.filter(c => c.id !== id);
    } catch (e) { alert('ลบไม่ได้: มีเมนูค้างอยู่'); }
}

// ✨ ฟังก์ชันเลื่อนลำดับ
const moveCategory = (index, direction) => {
    if (direction === 'up' && index > 0) {
        // สลับกับตัวบน
        [categories.value[index], categories.value[index - 1]] = [categories.value[index - 1], categories.value[index]];
    } else if (direction === 'down' && index < categories.value.length - 1) {
        // สลับกับตัวล่าง
        [categories.value[index], categories.value[index + 1]] = [categories.value[index + 1], categories.value[index]];
    }
    // สั่งบันทึกเข้า Database ทันที
    saveCategoryOrder();
}

// ✨ ฟังก์ชันบันทึกลำดับไป Backend
const saveCategoryOrder = async () => {
    try {
        // ส่งทั้งก้อนไปให้ Backend วนลูป update sort_order ตาม index
        await axios.post('/api/admin/categories/reorder', {
            categories: categories.value.map((c, i) => ({ id: c.id, sort_order: i + 1 }))
        });
    } catch (e) { console.error("Reorder failed", e); }
}

// --- Menu Management ---
const openModal = (product = null) => {
    if (product) {
        isEditing.value = true;
        form.value = { ...product, description: product.description || '', image: null };
    } else {
        isEditing.value = false;
        form.value = { name: '', description: '', price: '', category_id: '', image: null };
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
        formData.append('description', form.value.description);
        if (form.value.image) formData.append('image', form.value.image);

        if (isEditing.value) await axios.post(`/api/products/${form.value.id}`, formData);
        else await axios.post('/api/products', formData);

        closeModal();
        fetchInitialData();
    } catch (e) { alert('เกิดข้อผิดพลาด'); } finally { loading.value = false; }
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
