<template>
  <div style="height: 100vh; display: flex; justify-content: center; align-items: center; background: #f3f4f6;">
    <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px;">

      <h1 style="text-align: center; font-size: 24px; font-weight: bold; color: #1f2937; margin-bottom: 30px;">
        🔐 เข้าสู่ระบบ POS
      </h1>

      <form @submit.prevent="handleLogin">
        <!-- เปลี่ยนจาก Email เป็น Username -->
        <div style="margin-bottom: 20px;">
          <label style="display: block; margin-bottom: 5px; color: #4b5563;">ชื่อผู้ใช้งาน (Username)</label>
          <input v-model="form.username" type="text" required placeholder="เช่น admin" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
        </div>

        <div style="margin-bottom: 30px;">
          <label style="display: block; margin-bottom: 5px; color: #4b5563;">รหัสผ่าน</label>
          <input v-model="form.password" type="password" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
        </div>

        <button
          type="submit"
          :disabled="loading"
          style="width: 100%; background: #2563eb; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;"
        >
          {{ loading ? 'กำลังตรวจสอบ...' : 'เข้าสู่ระบบ' }}
        </button>
      </form>

      <p v-if="error" style="color: #ef4444; text-align: center; margin-top: 15px;">{{ error }}</p>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const form = ref({ username: '', password: '' });
const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
    loading.value = true;
    error.value = '';

    try {
        // 1. ยิง API Login
        const response = await axios.post('/login', form.value);

        // 2. ดึง Role ออกมาจากข้อมูลที่ Backend ส่งกลับมา
        const user = response.data.user;
        const role = user.role;

        // 3. เช็คเงื่อนไข แล้วพาไปให้ถูกหน้า
        if (role === 'owner') {
            window.location.href = '/admin/dashboard';
        }
        else if (role === 'kitchen') {
            window.location.href = '/kitchen';
        }
        else if (role === 'waiter') {
            window.location.href = '/waiter';
        }
        else {
            // กรณีอื่นๆ หรือกันพลาด
            window.location.href = '/';
        }

    } catch (e) {
        console.error(e);
        if (e.response && e.response.status === 401) {
            error.value = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        } else {
            error.value = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
        }
    } finally {
        loading.value = false;
    }
}
</script>
