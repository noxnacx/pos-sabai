import './bootstrap';
import { createApp } from 'vue';

import ProductList from './components/ProductList.vue';
import CustomerMenu from './components/CustomerMenu.vue';
import KitchenDisplay from './components/KitchenDisplay.vue';
import WaiterDisplay from './components/WaiterDisplay.vue';
import AdminMenu from './components/AdminMenu.vue';
import AdminDashboard from './components/AdminDashboard.vue';
import Login from './components/Login.vue';
import UserManagement from './components/UserManagement.vue';
import AdminTables from './components/AdminTables.vue';
import AdminReports from './components/AdminReports.vue';

// สร้างแอป Vue
const app = createApp({});


app.component('product-list', ProductList);
app.component('customer-menu', CustomerMenu);
app.component('kitchen-display', KitchenDisplay);
app.component('waiter-display', WaiterDisplay);
app.component('admin-menu', AdminMenu);
app.component('admin-dashboard', AdminDashboard);
app.component('login', Login);
app.component('user-management', UserManagement);
app.component('admin-tables', AdminTables);
app.component('admin-reports', AdminReports);

// สั่งให้ Vue ไปสิงร่างที่ html tag ที่มี id="app"
app.mount('#app');
