<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BÀI THỰC HÀNH SỐ 5 - Dịch vụ Báo cáo & Thống kê (SOA)</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .code-font { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">

    <!-- Top Header -->
    <header class="bg-slate-900 text-white shadow-lg border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-3">
                        <span class="px-2.5 py-1 bg-indigo-700 text-indigo-100 rounded text-xs font-semibold uppercase tracking-wider">BÀI THỰC HÀNH SỐ 5</span>
                        <span class="px-2.5 py-1 bg-emerald-600 text-white rounded text-xs font-semibold">SOA Architecture</span>
                        <span class="px-2.5 py-1 bg-violet-600 text-white rounded text-xs font-semibold">Port 8003</span>
                    </div>
                    <h1 class="text-2xl font-bold mt-2">Dịch Vụ Báo Cáo & Thống Kê (Reporting & BI Analytics)</h1>
                    <p class="text-slate-300 text-sm mt-1">
                        Tổng hợp liên dịch vụ: Thống kê <strong>Hàng tồn, Hàng bán, Doanh thu, Chi phí, Lợi nhuận</strong> theo Sản phẩm và Đơn hàng
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="/health" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded text-xs font-medium text-slate-200 border border-slate-700 transition">
                        <i class="fa-solid fa-heart-pulse mr-1 text-emerald-400"></i> Service Health
                    </a>
                    <a href="#console-section" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 rounded text-xs font-medium text-slate-200 border border-slate-700 transition">
                        <i class="fa-solid fa-terminal mr-1 text-amber-400"></i> API Log Console
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- 1. SOA Ecosystem 4-Service Status Grid -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase">Service 1 (Bài 2)</span>
                    <span id="badge-auth" class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Checking</span>
                </div>
                <div class="font-bold text-slate-800 text-sm">Auth Service (Port 8000)</div>
                <div class="text-xs text-slate-400 mt-1">Quản lý định danh & JWT</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase">Service 2 (Bài 3)</span>
                    <span id="badge-product" class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Checking</span>
                </div>
                <div class="font-bold text-slate-800 text-sm">Product Service (Port 8001)</div>
                <div class="text-xs text-slate-400 mt-1">Cung cấp dữ liệu hàng tồn kho</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase">Service 3 (Bài 4)</span>
                    <span id="badge-order" class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Checking</span>
                </div>
                <div class="font-bold text-slate-800 text-sm">Order Service (Port 8002)</div>
                <div class="text-xs text-slate-400 mt-1">Cung cấp dữ liệu hàng bán được</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-violet-200 bg-violet-50/30 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-violet-700 uppercase">Service 4 (Bài 5)</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Online</span>
                </div>
                <div class="font-bold text-violet-900 text-sm">Reporting Service (Port 8003)</div>
                <div class="text-xs text-violet-600 mt-1">Tổng hợp BI & Lưu Snapshot</div>
            </div>
        </section>

        <!-- 2. Authentication Bar -->
        <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-id-badge"></i>
                </div>
                <div>
                    <div class="text-xs font-medium text-slate-500">Tài khoản xác thực liên dịch vụ (Auth Service Port 8000)</div>
                    <div id="user-info-text" class="text-sm font-bold text-slate-800">student (ID: 1)</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="loginAuthService()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-semibold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-key"></i> Đăng Nhập Service 2
                </button>
                <button onclick="fetchAllReports()" class="px-3.5 py-1.5 bg-violet-600 hover:bg-violet-700 text-white rounded text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-rotate"></i> Cập Nhật Tất Cả Báo Cáo
                </button>
                <button onclick="openSaveSnapshotModal()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Snapshot Báo Cáo
                </button>
            </div>
        </section>

        <!-- 3. KPI Summary Metric Cards (6 Thống kê cốt lõi của đề bài) -->
        <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- 1. Hàng tồn -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium mb-1">
                    <span>Hàng tồn kho</span>
                    <i class="fa-solid fa-boxes-stacked text-blue-500"></i>
                </div>
                <div id="kpi-inventory" class="text-2xl font-bold text-slate-900">---</div>
                <div class="text-xs text-slate-400 mt-1">từ Product Service</div>
            </div>

            <!-- 2. Hàng bán được -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium mb-1">
                    <span>Hàng bán được</span>
                    <i class="fa-solid fa-cart-shopping text-emerald-500"></i>
                </div>
                <div id="kpi-sold" class="text-2xl font-bold text-emerald-700">---</div>
                <div class="text-xs text-slate-400 mt-1">từ Order Service</div>
            </div>

            <!-- 3. Doanh thu -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium mb-1">
                    <span>Tổng Doanh thu</span>
                    <i class="fa-solid fa-money-bill-trend-up text-amber-500"></i>
                </div>
                <div id="kpi-revenue" class="text-lg font-bold text-amber-700 truncate">---</div>
                <div class="text-xs text-slate-400 mt-1">Đơn hàng thành công</div>
            </div>

            <!-- 4. Chi phí -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium mb-1">
                    <span>Tổng Chi phí</span>
                    <i class="fa-solid fa-file-invoice-dollar text-rose-500"></i>
                </div>
                <div id="kpi-cost" class="text-lg font-bold text-rose-700 truncate">---</div>
                <div class="text-xs text-slate-400 mt-1">Giá vốn (70% doanh thu)</div>
            </div>

            <!-- 5. Lợi nhuận -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium mb-1">
                    <span>Tổng Lợi nhuận</span>
                    <i class="fa-solid fa-sack-dollar text-violet-500"></i>
                </div>
                <div id="kpi-profit" class="text-lg font-bold text-violet-700 truncate">---</div>
                <div class="text-xs text-slate-400 mt-1">Doanh thu - Chi phí</div>
            </div>

            <!-- 6. Tỷ suất LN -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between text-slate-500 text-xs font-medium mb-1">
                    <span>Tỷ suất Lợi nhuận</span>
                    <i class="fa-solid fa-percent text-sky-500"></i>
                </div>
                <div id="kpi-margin" class="text-2xl font-bold text-sky-700">---</div>
                <div class="text-xs text-slate-400 mt-1">Biên lợi nhuận ròng</div>
            </div>
        </section>

        <!-- 4. Tabbed Analytics: Báo cáo theo Sản phẩm & Báo cáo theo Đơn hàng -->
        <section class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <button id="tab-btn-products" onclick="switchTab('products')" class="px-4 py-2 font-bold text-sm text-violet-700 border-b-2 border-violet-600 transition flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked"></i> Báo Cáo Theo Sản Phẩm
                    </button>
                    <button id="tab-btn-orders" onclick="switchTab('orders')" class="px-4 py-2 font-semibold text-sm text-slate-500 hover:text-slate-700 transition flex items-center gap-2">
                        <i class="fa-solid fa-receipt"></i> Báo Cáo Theo Đơn Hàng
                    </button>
                    <button id="tab-btn-snapshots" onclick="switchTab('snapshots')" class="px-4 py-2 font-semibold text-sm text-slate-500 hover:text-slate-700 transition flex items-center gap-2">
                        <i class="fa-solid fa-database"></i> Lịch Sử Bản Lưu Snapshot
                    </button>
                </div>
                <div class="text-xs text-slate-400">
                    Cập nhật lần cuối: <span id="last-updated-text" class="text-slate-600 font-medium">---</span>
                </div>
            </div>

            <!-- Tab 1: Báo cáo theo sản phẩm -->
            <div id="tab-content-products" class="p-6">
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-100 text-slate-700 font-semibold text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left w-16">ID</th>
                                <th class="py-3 px-4 text-left">Tên sản phẩm</th>
                                <th class="py-3 px-4 text-right">Đơn giá</th>
                                <th class="py-3 px-4 text-center">Tồn kho</th>
                                <th class="py-3 px-4 text-center">Đã bán</th>
                                <th class="py-3 px-4 text-right">Doanh thu</th>
                                <th class="py-3 px-4 text-right">Chi phí vốn</th>
                                <th class="py-3 px-4 text-right">Lợi nhuận</th>
                                <th class="py-3 px-4 text-center">Trạng thái kho</th>
                            </tr>
                        </thead>
                        <tbody id="products-table-body" class="divide-y divide-slate-200 bg-white">
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang tổng hợp dữ liệu sản phẩm...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Báo cáo theo đơn hàng -->
            <div id="tab-content-orders" class="p-6 hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-xs text-slate-500 block">Tổng số đơn hàng</span>
                        <span id="order-kpi-total" class="text-lg font-bold text-slate-800">---</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-xs text-slate-500 block">Đơn thành công</span>
                        <span id="order-kpi-success" class="text-lg font-bold text-emerald-700">---</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-xs text-slate-500 block">Giá trị TB đơn (AOV)</span>
                        <span id="order-kpi-aov" class="text-lg font-bold text-sky-700">---</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-xs text-slate-500 block">Tỷ lệ thành công</span>
                        <span id="order-kpi-rate" class="text-lg font-bold text-violet-700">---</span>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-100 text-slate-700 font-semibold text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left w-16">Mã Đơn</th>
                                <th class="py-3 px-4 text-left">Khách hàng</th>
                                <th class="py-3 px-4 text-left">Sản phẩm</th>
                                <th class="py-3 px-4 text-center">SL</th>
                                <th class="py-3 px-4 text-right">Doanh thu</th>
                                <th class="py-3 px-4 text-right">Chi phí</th>
                                <th class="py-3 px-4 text-right">Lợi nhuận</th>
                                <th class="py-3 px-4 text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table-body" class="divide-y divide-slate-200 bg-white">
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang tổng hợp dữ liệu đơn hàng...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Lịch sử Bản lưu Snapshot -->
            <div id="tab-content-snapshots" class="p-6 hidden">
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-100 text-slate-700 font-semibold text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left w-16">ID</th>
                                <th class="py-3 px-4 text-left">Tiêu đề bản lưu</th>
                                <th class="py-3 px-4 text-center">Tồn kho</th>
                                <th class="py-3 px-4 text-center">Đã bán</th>
                                <th class="py-3 px-4 text-right">Doanh thu</th>
                                <th class="py-3 px-4 text-right">Lợi nhuận</th>
                                <th class="py-3 px-4 text-center">Người tạo</th>
                                <th class="py-3 px-4 text-center">Thời gian lưu</th>
                                <th class="py-3 px-4 text-center w-24">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="snapshots-table-body" class="divide-y divide-slate-200 bg-white">
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang nạp danh sách bản lưu...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- 5. Real-time API Monitor Console -->
        <section id="console-section" class="bg-slate-900 rounded-xl shadow-lg border border-slate-800 p-6 text-slate-100">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                    <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                    <span class="text-xs font-semibold text-slate-400 ml-2">RESTful API Request / Response Monitor (SOA Reporting Aggregation)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span id="api-method-badge" class="px-2 py-0.5 rounded text-xs font-bold bg-slate-800 text-slate-300">IDLE</span>
                    <span id="api-status-badge" class="px-2 py-0.5 rounded text-xs font-bold bg-slate-800 text-slate-300">---</span>
                </div>
            </div>
            <div class="text-xs code-font text-slate-400 mb-2 truncate">
                <span class="text-slate-500">Target Endpoint: </span>
                <span id="api-url-text" class="text-violet-400">Chưa có request nào</span>
            </div>
            <pre id="api-response-body" class="p-4 rounded-lg bg-slate-950 text-emerald-400 code-font text-xs overflow-x-auto max-h-80 border border-slate-800 leading-relaxed">// JSON Response từ Reporting Service sẽ xuất hiện tại đây...</pre>
        </section>

    </main>

    <!-- Modal Save Snapshot -->
    <div id="snapshot-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-emerald-600"></i> Lưu Bản Chụp Báo Cáo
                </h3>
                <button onclick="closeSaveSnapshotModal()" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form onsubmit="handleSaveSnapshot(event)" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tiêu đề báo cáo <span class="text-rose-500">*</span></label>
                    <input type="text" id="snapshot-title" required value="Báo cáo kết quả kinh doanh quý 4" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ghi chú</label>
                    <textarea id="snapshot-notes" rows="3" placeholder="Nhập ghi chú hoặc đánh giá..." class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:outline-none"></textarea>
                </div>
                <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" onclick="closeSaveSnapshotModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition">Hủy</button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition">Lưu Vào CSDL</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Application Logic -->
    <script>
        const AUTH_BASE_URL = 'http://localhost:8000';
        const PRODUCT_BASE_URL = 'http://localhost:8001';
        const ORDER_BASE_URL = 'http://localhost:8002';
        const REPORT_BASE_URL = 'http://localhost:8003';

        let currentToken = localStorage.getItem('soa_jwt_token') || '';

        document.addEventListener('DOMContentLoaded', () => {
            checkServicesStatus();
            fetchAllReports();
            loadSnapshots();
        });

        async function checkServicesStatus() {
            // Check Auth (8000)
            try {
                const res = await fetch(`${AUTH_BASE_URL}/`, { method: 'GET' });
                setBadge('badge-auth', true, 'Online');
            } catch (e) { setBadge('badge-auth', false, 'Offline'); }

            // Check Product (8001)
            try {
                const res = await fetch(`${PRODUCT_BASE_URL}/health`, { method: 'GET' });
                setBadge('badge-product', res.ok, res.ok ? 'Online' : 'Offline');
            } catch (e) { setBadge('badge-product', false, 'Offline'); }

            // Check Order (8002)
            try {
                const res = await fetch(`${ORDER_BASE_URL}/health`, { method: 'GET' });
                setBadge('badge-order', res.ok, res.ok ? 'Online' : 'Offline');
            } catch (e) { setBadge('badge-order', false, 'Offline'); }
        }

        function setBadge(id, isOnline, text) {
            const el = document.getElementById(id);
            if (isOnline) {
                el.className = 'px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
                el.textContent = text;
            } else {
                el.className = 'px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800';
                el.textContent = text;
            }
        }

        function logConsole(method, url, status, data) {
            const methodBadge = document.getElementById('api-method-badge');
            const statusBadge = document.getElementById('api-status-badge');
            const urlText = document.getElementById('api-url-text');
            const bodyEl = document.getElementById('api-response-body');

            methodBadge.textContent = method;
            methodBadge.className = 'px-2 py-0.5 rounded text-xs font-bold ' + 
                (method === 'GET' ? 'bg-sky-700 text-sky-100' : 
                 method === 'POST' ? 'bg-emerald-700 text-emerald-100' : 'bg-rose-700 text-rose-100');

            statusBadge.textContent = status + (status >= 200 && status < 300 ? ' OK' : ' ERROR');
            statusBadge.className = 'px-2 py-0.5 rounded text-xs font-bold ' +
                (status >= 200 && status < 300 ? 'bg-emerald-700 text-emerald-100' : 'bg-rose-700 text-rose-100');

            urlText.textContent = url;
            bodyEl.textContent = JSON.stringify(data, null, 2);
        }

        // Đăng nhập Service 2
        async function loginAuthService() {
            try {
                const response = await fetch(`${AUTH_BASE_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ userName: 'student', password: btoa('123456') })
                });
                const data = await response.json();
                if (response.ok && data.access_token) {
                    currentToken = data.access_token;
                    localStorage.setItem('soa_jwt_token', currentToken);
                    alert('Đăng nhập thành công! Token đã sẵn sàng.');
                    fetchAllReports();
                } else {
                    alert('Đăng nhập thất bại: ' + (data.message || 'Lỗi không xác định'));
                }
            } catch (err) {
                alert('Không thể kết nối đến Auth Service (Port 8000).');
            }
        }

        // Tải toàn bộ báo cáo từ Reporting Service
        async function fetchAllReports() {
            const headers = { 'Accept': 'application/json' };
            if (currentToken) headers['Authorization'] = 'Bearer ' + currentToken;

            try {
                const response = await fetch(`${REPORT_BASE_URL}/reports/full`, { headers });
                const data = await response.json();
                logConsole('GET', `${REPORT_BASE_URL}/reports/full`, response.status, data);

                if (response.ok && data.data) {
                    const full = data.data;
                    renderKPIs(full.summary);
                    renderProductReport(full.by_product);
                    renderOrderReport(full.by_order);
                    document.getElementById('last-updated-text').textContent = full.generated_at;
                } else {
                    alert('Lỗi nạp báo cáo (' + response.status + '): ' + (data.message || ''));
                }
            } catch (err) {
                logConsole('GET', `${REPORT_BASE_URL}/reports/full`, 0, { error: err.message });
            }
        }

        function renderKPIs(summary) {
            document.getElementById('kpi-inventory').textContent = summary.total_inventory;
            document.getElementById('kpi-sold').textContent = summary.total_sold;
            document.getElementById('kpi-revenue').textContent = formatCurrency(summary.total_revenue);
            document.getElementById('kpi-cost').textContent = formatCurrency(summary.total_cost);
            document.getElementById('kpi-profit').textContent = formatCurrency(summary.total_profit);
            document.getElementById('kpi-margin').textContent = summary.profit_margin + '%';
        }

        function renderProductReport(products) {
            const tbody = document.getElementById('products-table-body');
            if (!products || products.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="py-6 text-center text-slate-400">Không có dữ liệu sản phẩm.</td></tr>`;
                return;
            }

            tbody.innerHTML = products.map(p => `
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-4 font-bold text-slate-600">#${p.product_id}</td>
                    <td class="py-3 px-4 font-medium text-slate-900">${escapeHtml(p.product_name)}</td>
                    <td class="py-3 px-4 text-right text-xs text-slate-500">${formatCurrency(p.unit_price)}</td>
                    <td class="py-3 px-4 text-center font-bold text-blue-700">${p.current_stock}</td>
                    <td class="py-3 px-4 text-center font-bold text-emerald-700">${p.sold_quantity}</td>
                    <td class="py-3 px-4 text-right font-semibold text-slate-800">${formatCurrency(p.revenue)}</td>
                    <td class="py-3 px-4 text-right text-xs text-rose-600">${formatCurrency(p.cost)}</td>
                    <td class="py-3 px-4 text-right font-bold text-violet-700">${formatCurrency(p.profit)}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold ${p.current_stock > 5 ? 'bg-emerald-100 text-emerald-800' : p.current_stock > 0 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800'}">
                            ${p.stock_status}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        function renderOrderReport(orderData) {
            document.getElementById('order-kpi-total').textContent = orderData.total_orders;
            document.getElementById('order-kpi-success').textContent = 
                (orderData.status_breakdown.completed + orderData.status_breakdown.confirmed + orderData.status_breakdown.shipping);
            document.getElementById('order-kpi-aov').textContent = formatCurrency(orderData.average_order_value);
            document.getElementById('order-kpi-rate').textContent = orderData.success_rate + '%';

            const tbody = document.getElementById('orders-table-body');
            const orders = orderData.orders || [];

            if (orders.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="py-6 text-center text-slate-400">Không có dữ liệu đơn hàng.</td></tr>`;
                return;
            }

            tbody.innerHTML = orders.map(o => `
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-4 font-bold text-slate-700">#${o.id}</td>
                    <td class="py-3 px-4 font-medium text-slate-800">${escapeHtml(o.user_name)}</td>
                    <td class="py-3 px-4 text-slate-900">${escapeHtml(o.product_name)}</td>
                    <td class="py-3 px-4 text-center font-bold">${o.quantity}</td>
                    <td class="py-3 px-4 text-right font-semibold text-slate-800">${formatCurrency(o.total_price)}</td>
                    <td class="py-3 px-4 text-right text-xs text-rose-600">${formatCurrency(o.estimated_cost)}</td>
                    <td class="py-3 px-4 text-right font-bold text-violet-700">${formatCurrency(o.estimated_profit)}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${o.status === 'completed' ? 'bg-emerald-100 text-emerald-800' : o.status === 'cancelled' ? 'bg-rose-100 text-rose-800' : 'bg-sky-100 text-sky-800'}">
                            ${o.status}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        // Tải danh sách bản lưu Snapshot từ database riêng của Reporting Service
        async function loadSnapshots() {
            const tbody = document.getElementById('snapshots-table-body');
            try {
                const headers = { 'Accept': 'application/json' };
                if (currentToken) headers['Authorization'] = 'Bearer ' + currentToken;

                const res = await fetch(`${REPORT_BASE_URL}/reports/snapshots`, { headers });
                const data = await res.json();

                if (res.ok && data.data) {
                    const list = data.data;
                    if (list.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="9" class="py-6 text-center text-slate-400">Chưa có bản lưu báo cáo nào. Bấm "Lưu Snapshot Báo Cáo" ở trên.</td></tr>`;
                        return;
                    }
                    tbody.innerHTML = list.map(s => `
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-bold text-slate-700">#${s.id}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">${escapeHtml(s.title)}</td>
                            <td class="py-3 px-4 text-center font-bold text-blue-700">${s.total_inventory}</td>
                            <td class="py-3 px-4 text-center font-bold text-emerald-700">${s.total_sold}</td>
                            <td class="py-3 px-4 text-right font-semibold text-slate-800">${formatCurrency(s.total_revenue)}</td>
                            <td class="py-3 px-4 text-right font-bold text-violet-700">${formatCurrency(s.total_profit)}</td>
                            <td class="py-3 px-4 text-center text-xs text-slate-500">${escapeHtml(s.generated_by)}</td>
                            <td class="py-3 px-4 text-center text-xs text-slate-400">${formatDate(s.created_at)}</td>
                            <td class="py-3 px-4 text-center">
                                <button onclick="deleteSnapshot(${s.id})" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded transition" title="Xóa bản lưu">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                }
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="9" class="py-6 text-center text-rose-500">Lỗi nạp bản lưu snapshot: ${err.message}</td></tr>`;
            }
        }

        // Lưu snapshot vào CSDL
        async function handleSaveSnapshot(e) {
            e.preventDefault();
            const title = document.getElementById('snapshot-title').value.trim();
            const notes = document.getElementById('snapshot-notes').value.trim();

            try {
                const response = await fetch(`${REPORT_BASE_URL}/reports/snapshots`, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + currentToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ title, notes })
                });

                const data = await response.json();
                logConsole('POST', `${REPORT_BASE_URL}/reports/snapshots`, response.status, data);

                if (response.ok) {
                    alert('🎉 Đã lưu bản chụp báo cáo vào CSDL riêng của Reporting Service!');
                    closeSaveSnapshotModal();
                    loadSnapshots();
                } else {
                    alert('Lỗi lưu báo cáo: ' + (data.message || ''));
                }
            } catch (err) {
                alert('Lỗi kết nối: ' + err.message);
            }
        }

        async function deleteSnapshot(id) {
            if (!confirm(`Xóa bản lưu báo cáo #${id}?`)) return;

            try {
                const response = await fetch(`${REPORT_BASE_URL}/reports/snapshots/${id}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': 'Bearer ' + currentToken, 'Accept': 'application/json' }
                });
                const data = await response.json();
                logConsole('DELETE', `${REPORT_BASE_URL}/reports/snapshots/${id}`, response.status, data);

                if (response.ok) {
                    alert(`Đã xóa bản lưu #${id}!`);
                    loadSnapshots();
                }
            } catch (err) { alert('Lỗi: ' + err.message); }
        }

        // Switch Tabs
        function switchTab(tab) {
            const tabs = ['products', 'orders', 'snapshots'];
            tabs.forEach(t => {
                document.getElementById(`tab-content-${t}`).classList.add('hidden');
                const btn = document.getElementById(`tab-btn-${t}`);
                btn.className = 'px-4 py-2 font-semibold text-sm text-slate-500 hover:text-slate-700 transition flex items-center gap-2';
            });

            document.getElementById(`tab-content-${tab}`).classList.remove('hidden');
            const activeBtn = document.getElementById(`tab-btn-${tab}`);
            activeBtn.className = 'px-4 py-2 font-bold text-sm text-violet-700 border-b-2 border-violet-600 transition flex items-center gap-2';
        }

        function openSaveSnapshotModal() { document.getElementById('snapshot-modal').classList.remove('hidden'); }
        function closeSaveSnapshotModal() { document.getElementById('snapshot-modal').classList.add('hidden'); }
        function formatCurrency(val) { return Number(val).toLocaleString('vi-VN') + ' ₫'; }
        function formatDate(val) { return val ? new Date(val).toLocaleString('vi-VN') : '---'; }
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m]);
        }
    </script>
</body>
</html>
