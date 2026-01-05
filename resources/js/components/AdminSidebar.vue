<template>
  <aside class="w-64 bg-white border-r border-slate-200 flex flex-col h-screen fixed left-0 top-0 z-40 hidden lg:flex shadow-sm font-sans transition-all">

    <!-- 🟢 Brand Logo -->
    <div class="h-20 flex items-center px-8 border-b border-slate-50">
      <div class="text-2xl font-black text-indigo-600 flex items-center gap-2 tracking-tighter cursor-default">
        <span class="text-3xl">⚡</span> POS<span class="text-slate-800">System</span>
      </div>
    </div>

    <!-- 🟡 Menu Items -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">

      <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Admin Tools</p>

      <a href="/admin/dashboard" :class="['nav-item', isActive('/admin/dashboard') ? 'active' : '']">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
        Dashboard
      </a>

      <a href="/admin/menu" :class="['nav-item', isActive('/admin/menu') ? 'active' : '']">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
        จัดการเมนูอาหาร
      </a>

      <a href="/admin/users" :class="['nav-item', isActive('/admin/users') ? 'active' : '']">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        จัดการพนักงาน
      </a>
      <a href="/admin/tables" :class="['nav-item', isActive('/admin/tables') ? 'active' : '']">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
      จัดการโต๊ะอาหาร
      </a>
        <a href="/admin/reports" :class="['nav-item', isActive('/admin/reports') ? 'active' : '']">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
        รายงานผลประกอบการ
        </a>
      <div class="my-6 border-t border-slate-100"></div>

      <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Front Office</p>

      <a href="/kitchen" target="_blank" class="nav-item group">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 group-hover:text-orange-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" /></svg>
        จอห้องครัว (Kitchen)
      </a>

      <a href="/waiter" target="_blank" class="nav-item group">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 group-hover:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
        จอแคชเชียร์ (Cashier)
      </a>
    </nav>

    <!-- 🔴 Logout -->
    <div class="p-4 border-t border-slate-100">
      <button @click="logout" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl hover:bg-rose-50 text-slate-500 hover:text-rose-600 transition-all duration-200 group">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 01-3-3h4a3 3 0 013 3v1" /></svg>
        <span class="font-bold text-sm">ออกจากระบบ</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import axios from 'axios';

// เช็คว่า URL ปัจจุบันคือหน้าไหน เพื่อใส่สีปุ่ม Active
const isActive = (path) => window.location.pathname === path;

const logout = async () => {
  if(!confirm('ยืนยันออกจากระบบ?')) return;
  try {
    await axios.post('/logout');
    window.location.href = '/login';
  } catch (e) {
    alert('Logout failed');
  }
}
</script>

<style scoped>
/* สไตล์ปุ่มเมนู */
.nav-item {
  @apply flex items-center gap-3 px-4 py-3.5 text-slate-500 rounded-xl transition-all duration-200 font-medium text-sm;
}
/* เมนูตอนเอาเมาส์ชี้ */
.nav-item:hover {
  @apply bg-indigo-50 text-indigo-600 translate-x-1;
}
/* เมนูที่กำลังเลือกอยู่ (Active) */
.nav-item.active {
  @apply bg-indigo-600 text-white shadow-lg shadow-indigo-200;
}
</style>
