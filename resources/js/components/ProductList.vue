<template>
  <div style="display: flex; height: 100vh;">

    <div style="flex: 7; padding: 20px; background-color: #f3f4f6; overflow-y: auto;">
      <h1 style="color: #1f2937; font-size: 24px; font-weight: bold; margin-bottom: 20px;">
        🛒 ร้านค้า POS
      </h1>

      <div v-if="loading">กำลังโหลดสินค้า...</div>

      <div v-else style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
        <div
            v-for="product in products"
            :key="product.id"
            style="background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"
            >
            <div style="height: 150px; overflow: hidden; border-radius: 8px; margin-bottom: 10px;">
                <img
                :src="product.image_url || 'https://via.placeholder.com/300x200?text=No+Image'"
                style="width: 100%; height: 100%; object-fit: cover;"
                >
            </div>

            <h3 style="font-weight: bold; font-size: 18px;">{{ product.name }}</h3>
            <p style="color: #6b7280; font-size: 14px;">{{ product.category?.name }}</p>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <span style="color: #059669; font-weight: bold; font-size: 18px;">฿{{ product.price }}</span>
                <button
                @click="addToCart(product)"
                style="background: #2563eb; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;"
                >
                + เพิ่ม
                </button>
            </div>
            </div>
      </div>
    </div>

    <div style="flex: 3; background: white; padding: 20px; border-left: 1px solid #ddd; display: flex; flex-direction: column;">
      <h2 style="font-size: 20px; font-weight: bold; margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
        🧾 รายการสั่งซื้อ
      </h2>

      <div style="flex: 1; overflow-y: auto;">
        <div v-if="cart.length === 0" style="text-align: center; color: #9ca3af; margin-top: 50px;">
          ยังไม่มีสินค้าในตะกร้า
        </div>

        <div
          v-for="(item, index) in cart"
          :key="index"
          style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #eee;"
        >
          <div>
            <div style="font-weight: bold;">{{ item.name }}</div>
            <div style="font-size: 12px; color: #6b7280;">฿{{ item.price }} x {{ item.qty }}</div>
          </div>
          <div style="text-align: right;">
            <div style="font-weight: bold;">฿{{ item.price * item.qty }}</div>
            <button
              @click="removeFromCart(index)"
              style="color: red; background: none; border: none; font-size: 12px; cursor: pointer;"
            >
              ลบ
            </button>
          </div>
        </div>
      </div>

      <div style="margin-top: 20px; background: #f9fafb; padding: 15px; border-radius: 10px;">
        <div style="display: flex; justify-content: space-between; font-size: 18px; margin-bottom: 10px;">
          <span>ยอดรวม</span>
          <span style="font-weight: bold;">฿{{ totalAmount }}</span>
        </div>
        <button
          style="width: 100%; background: #059669; color: white; border: none; padding: 15px; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer;"
          @click="checkout"
        >
          ชำระเงิน
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const products = ref([]);
const cart = ref([]); // ตะกร้าสินค้า
const loading = ref(true);

// ดึงสินค้าจาก API
const fetchProducts = async () => {
    try {
        const response = await axios.get('/api/products');
        products.value = response.data.data;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
}

// ฟังก์ชัน: เพิ่มสินค้าลงตะกร้า
const addToCart = (product) => {
    // เช็คว่ามีสินค้านี้ในตะกร้าหรือยัง?
    const existingItem = cart.value.find(item => item.id === product.id);

    if (existingItem) {
        // ถ้ามีแล้ว ให้บวกจำนวนเพิ่ม
        existingItem.qty++;
    } else {
        // ถ้ายังไม่มี ให้เพิ่มรายการใหม่ โดยตั้ง qty = 1
        cart.value.push({
            ...product, // ก๊อปปี้ข้อมูลสินค้ามา
            qty: 1
        });
    }
}

// ฟังก์ชัน: ลบสินค้า
const removeFromCart = (index) => {
    cart.value.splice(index, 1);
}

// ฟังก์ชัน: คำนวณยอดรวม (Computed Property)
const totalAmount = computed(() => {
    // วนลูปบวกเงิน (ราคา * จำนวน)
    return cart.value.reduce((sum, item) => sum + (item.price * item.qty), 0);
});

// ฟังก์ชัน: จำลองการจ่ายเงิน
const checkout = () => {
    if (cart.value.length === 0) return alert('กรุณาเลือกสินค้าก่อน');
    alert(`รับเงินทั้งหมด ${totalAmount.value} บาท ขอบคุณครับ!`);
    cart.value = []; // ล้างตะกร้า
}

onMounted(() => {
    fetchProducts();
});
</script>
