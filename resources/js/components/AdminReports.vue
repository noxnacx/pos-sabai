<template>
  <div class="min-h-screen bg-slate-50 font-sans">

    <admin-sidebar></admin-sidebar>

    <div class="lg:pl-64 transition-all duration-300">

        <!-- Header -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <h1 class="text-xl font-bold text-slate-800 tracking-tight">รายงานผลประกอบการ</h1>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto p-6 pb-20 space-y-6">

            <!-- 📊 Sales Chart Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-slate-800">ยอดขายรวม (Sales Revenue)</h2>
                    <div class="join">
                        <button @click="loadSales('daily')" :class="['btn btn-sm join-item', period === 'daily' ? 'btn-active btn-primary' : '']">รายวัน</button>
                        <button @click="loadSales('monthly')" :class="['btn btn-sm join-item', period === 'monthly' ? 'btn-active btn-primary' : '']">รายเดือน</button>
                    </div>
                </div>

                <div class="h-80 w-full relative">
                    <Bar v-if="chartData" :data="chartData" :options="chartOptions" />
                    <div v-else class="flex justify-center items-center h-full text-slate-400">กำลังโหลดกราฟ...</div>
                </div>
            </div>

            <!-- 🏆 Product Performance Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Best Sellers -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-emerald-50/50 flex items-center gap-2">
                        <span class="text-xl">🏆</span>
                        <h3 class="font-bold text-slate-800">10 เมนูขายดีที่สุด</h3>
                    </div>
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>ลำดับ</th>
                                <th>เมนู</th>
                                <th class="text-right">ขายได้ (ชิ้น)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in productStats.best_sellers" :key="index" class="hover">
                                <td class="font-bold text-slate-400">{{ index + 1 }}</td>
                                <td class="font-bold text-slate-700">{{ item.product_name }}</td>
                                <td class="text-right text-emerald-600 font-bold">{{ item.total_qty }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Low Sellers -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-red-50/50 flex items-center gap-2">
                        <span class="text-xl">📉</span>
                        <h3 class="font-bold text-slate-800">เมนูที่ควรปรับปรุง (ขายน้อย)</h3>
                    </div>
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>เมนู</th>
                                <th class="text-right">ขายได้ (ชิ้น)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in productStats.low_sellers" :key="index" class="hover">
                                <td class="font-bold text-slate-700">{{ item.product_name }}</td>
                                <td class="text-right text-red-500 font-bold">{{ item.total_qty }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminSidebar from './AdminSidebar.vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const period = ref('daily');
const chartData = ref(null);
const productStats = ref({ best_sellers: [], low_sellers: [] });

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    }
};

const loadSales = async (p = 'daily') => {
    period.value = p;
    try {
        const res = await axios.get(`/api/admin/reports/sales?period=${p}`);
        const data = res.data;

        chartData.value = {
            labels: data.map(d => d.date), // วันที่
            datasets: [{
                label: 'ยอดขาย (บาท)',
                data: data.map(d => d.total), // ยอดเงิน
                backgroundColor: '#4F46E5', // สีม่วง Indigo
                borderRadius: 5
            }]
        };
    } catch (e) { console.error(e); }
}

const loadProductStats = async () => {
    try {
        const res = await axios.get('/api/admin/reports/products');
        productStats.value = res.data;
    } catch (e) { console.error(e); }
}

onMounted(() => {
    loadSales('daily');
    loadProductStats();
});
</script>
