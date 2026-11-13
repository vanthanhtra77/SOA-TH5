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
                    <h1 class="text-2xl font-bold mt-2">Dịch Vụ Báo Cáo & Thống Kê (Reporting Service)</h1>
                    <p class="text-slate-300 text-sm mt-1">
                        CSDL riêng (<code class="text-amber-400">orders_reports</code> & <code class="text-amber-400">product_reports</code>) • Thống kê <strong>Hàng tồn, Hàng bán, Doanh thu, Chi phí, Lợi nhuận</strong>
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
                    <span class="text-xs font-semibold text-slate-500 uppercase">1. Auth Service</span>
                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded text-xs font-bold">Port 8000</span>
                </div>
                <div class="font-bold text-slate-800 text-sm">Xác thực JWT</div>
                <div id="auth-status-text" class="text-xs text-amber-600 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle text-[8px]"></i> Chờ đăng nhập
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase">2. Product Service</span>
                    <span class="px-2 py-0.5 bg-sky-100 text-sky-800 rounded text-xs font-bold">Port 8001</span>
                </div>
                <div class="font-bold text-slate-800 text-sm">Tồn kho & Giá</div>
                <div class="text-xs text-emerald-600 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle text-[8px]"></i> CSDL: products.sqlite
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase">3. Order Service</span>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded text-xs font-bold">Port 8002</span>
                </div>
                <div class="font-bold text-slate-800 text-sm">Đơn hàng & Bán</div>
                <div class="text-xs text-emerald-600 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle text-[8px]"></i> CSDL: orders.sqlite
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase">4. Reporting</span>
                    <span class="px-2 py-0.5 bg-violet-100 text-violet-800 rounded text-xs font-bold">Port 8003</span>
                </div>
                <div class="font-bold text-violet-700 text-sm">Báo cáo & Tổng hợp</div>
                <div class="text-xs text-emerald-600 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle text-[8px]"></i> CSDL: reports.sqlite
                </div>
            </div>
        </section>

        <!-- 2. Authentication Bar -->
        <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-indigo-600"></i>
                        Xác Thực Liên Dịch Vụ (Auth Service - Port 8000)
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Đăng nhập tài khoản mẫu <code class="text-indigo-600 code-font">student</code> (mật khẩu <code class="code-font">123456</code>) để lấy JWT Bearer Token</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button onclick="loginSampleUser()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-2">
                        <i class="fa-solid fa-key"></i> Đăng nhập & Lấy Token mẫu
                    </button>
                    <button onclick="clearToken()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition border border-slate-300">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
                    </button>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-2 text-xs">
                <div class="truncate max-w-2xl">
                    <span class="text-slate-500 font-semibold">Active Token: </span>
                    <span id="token-display" class="code-font text-slate-400 italic">Chưa có token (Vui lòng bấm Đăng nhập)</span>
                </div>
                <div id="user-info-display" class="font-medium text-slate-600">Người dùng: Chưa xác thực</div>
            </div>
        </section>

        <!-- 3. Navigation Tabs -->
        <div class="flex border-b border-slate-200">
            <button id="tab-products-btn" onclick="switchTab('products')" class="py-3 px-6 text-sm font-bold border-b-2 border-violet-600 text-violet-600 flex items-center gap-2">
                <i class="fa-solid fa-box"></i> 1. Báo Cáo Sản Phẩm (<code class="code-font">product_reports</code>)
            </button>
            <button id="tab-orders-btn" onclick="switchTab('orders')" class="py-3 px-6 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-receipt"></i> 2. Báo Cáo Đơn Hàng (<code class="code-font">orders_reports</code>)
            </button>
            <button id="tab-summary-btn" onclick="switchTab('summary')" class="py-3 px-6 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie"></i> 3. KPI Tổng Quan Real-time
            </button>
        </div>

        <!-- 4. Tab 1: Product Reports Section -->
        <section id="tab-products-section" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Báo Cáo Theo Sản Phẩm (Bảng <code class="text-violet-700 code-font">product_reports</code>)</h2>
                    <p class="text-xs text-slate-500">Gồm: <code class="code-font">id</code>, <code class="code-font">order_report_id</code>, <code class="code-font">product_id</code>, <code class="code-font">total_sold</code>, <code class="code-font">revenue</code>, <code class="code-font">cost</code>, <code class="code-font">profit</code></p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="loadProductReports()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 border border-slate-300">
                        <i class="fa-solid fa-rotate"></i> Tải lại danh sách
                    </button>
                    <button onclick="openCreateProductReportModal()" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus"></i> Tạo Báo Cáo Sản Phẩm Mới (POST)
                    </button>
                </div>
            </div>

            <!-- Product Reports Table -->
            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-100 text-slate-700 font-semibold text-xs">
                        <tr>
                            <th class="py-3 px-4 text-left">ID Báo Cáo</th>
                            <th class="py-3 px-4 text-left">Mã Báo Cáo Đơn (order_report_id)</th>
                            <th class="py-3 px-4 text-left">Mã SP (product_id)</th>
                            <th class="py-3 px-4 text-center">Đã Bán (total_sold)</th>
                            <th class="py-3 px-4 text-right">Doanh Thu (revenue)</th>
                            <th class="py-3 px-4 text-right">Chi Phí (cost)</th>
                            <th class="py-3 px-4 text-right">Lợi Nhuận (profit)</th>
                            <th class="py-3 px-4 text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="product-reports-table-body" class="divide-y divide-slate-200 bg-white">
                        <tr><td colspan="8" class="py-8 text-center text-slate-400">Đang nạp báo cáo sản phẩm...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 5. Tab 2: Order Reports Section -->
        <section id="tab-orders-section" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6 hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Báo Cáo Theo Đơn Hàng (Bảng <code class="text-violet-700 code-font">orders_reports</code>)</h2>
                    <p class="text-xs text-slate-500">Gồm: <code class="code-font">id</code>, <code class="code-font">order_id</code>, <code class="code-font">total_revenue</code>, <code class="code-font">total_cost</code>, <code class="code-font">total_profit</code></p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="loadOrderReports()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 border border-slate-300">
                        <i class="fa-solid fa-rotate"></i> Tải lại danh sách
                    </button>
                    <button onclick="openCreateOrderReportModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus"></i> Tạo Báo Cáo Đơn Hàng Mới (POST)
                    </button>
                </div>
            </div>

            <!-- Order Reports Table -->
            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-100 text-slate-700 font-semibold text-xs">
                        <tr>
                            <th class="py-3 px-4 text-left">ID Báo Cáo</th>
                            <th class="py-3 px-4 text-left">Mã Đơn Hàng (order_id)</th>
                            <th class="py-3 px-4 text-right">Tổng Doanh Thu (total_revenue)</th>
                            <th class="py-3 px-4 text-right">Tổng Chi Phí (total_cost)</th>
                            <th class="py-3 px-4 text-right">Tổng Lợi Nhuận (total_profit)</th>
                            <th class="py-3 px-4 text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="order-reports-table-body" class="divide-y divide-slate-200 bg-white">
                        <tr><td colspan="6" class="py-8 text-center text-slate-400">Đang nạp báo cáo đơn hàng...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 6. Tab 3: KPI Summary Section -->
        <section id="tab-summary-section" class="space-y-6 hidden">
            <!-- 6 KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase">Hàng Tồn Kho</div>
                    <div id="kpi-inventory" class="text-2xl font-bold text-slate-800 mt-1">---</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Sản phẩm hiện còn</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase">Hàng Đã Bán</div>
                    <div id="kpi-sold" class="text-2xl font-bold text-indigo-600 mt-1">---</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Sản phẩm tiêu thụ</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase">Tổng Doanh Thu</div>
                    <div id="kpi-revenue" class="text-xl font-bold text-sky-600 mt-1">---</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Tổng giá trị đơn</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase">Tổng Chi Phí</div>
                    <div id="kpi-cost" class="text-xl font-bold text-amber-600 mt-1">---</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Giá vốn (70%)</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase">Tổng Lợi Nhuận</div>
                    <div id="kpi-profit" class="text-xl font-bold text-emerald-600 mt-1">---</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Doanh thu - Chi phí</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                    <div class="text-xs font-semibold text-slate-500 uppercase">Biên Lợi Nhuận</div>
                    <div id="kpi-margin" class="text-2xl font-bold text-purple-600 mt-1">---</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Tỷ suất sinh lời</div>
                </div>
            </div>
            <div class="flex justify-end">
                <button onclick="loadSummaryKpi()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-semibold transition">
                    <i class="fa-solid fa-rotate mr-1"></i> Tải lại KPI Thời Gian Thực
                </button>
            </div>
        </section>

        <!-- 7. Console Log Section -->
        <section id="console-section" class="bg-slate-900 rounded-xl shadow-lg border border-slate-800 p-5 text-white">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-terminal text-amber-400"></i>
                    <h3 class="font-bold text-sm tracking-wide">API Response Console (Real-time)</h3>
                </div>
                <button onclick="clearConsole()" class="text-xs text-slate-400 hover:text-white transition">Xóa màn hình</button>
            </div>
            <pre id="console-output" class="mt-4 p-4 bg-slate-950 rounded-lg code-font text-xs text-emerald-400 overflow-x-auto max-h-72 leading-relaxed">// Nhật ký phản hồi API...</pre>
        </section>

    </main>

    <!-- Modal: Tạo Báo Cáo Sản Phẩm Mới (POST /reports/products) -->
    <div id="product-report-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200 max-w-md w-full mx-4 p-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-base">Tạo Báo Cáo Sản Phẩm (POST /reports/products)</h3>
                <button onclick="closeProductReportModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="create-product-report-form" onsubmit="handleCreateProductReport(event)" class="space-y-4 mt-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mã sản phẩm (product_id) *</label>
                    <select id="rep-product-id" required class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-violet-500 focus:outline-none">
                        <option value="1">iPhone 15 Pro Max (ID 1)</option>
                        <option value="2">Laptop Dell XPS 15 (ID 2)</option>
                        <option value="3">Bàn phím cơ Keychron K2 Pro (ID 3)</option>
                        <option value="4">Chuột không dây MX Master 3S (ID 4)</option>
                        <option value="5">Màn hình Dell UltraSharp 4K (ID 5)</option>
                        <option value="6">Tai nghe Sony WH-1000XM5 (ID 6)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Liên kết Báo cáo đơn hàng (order_report_id, tùy chọn)</label>
                    <input type="number" id="rep-order-report-id" placeholder="Để trống nếu là báo cáo chung" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-violet-500 focus:outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeProductReportModal()" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 font-medium">Hủy bỏ</button>
                    <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg font-semibold shadow-sm">Tạo báo cáo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Tạo Báo Cáo Đơn Hàng Mới (POST /reports/orders) -->
    <div id="order-report-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl shadow-2xl border border-slate-200 max-w-md w-full mx-4 p-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-base">Tạo Báo Cáo Đơn Hàng (POST /reports/orders)</h3>
                <button onclick="closeOrderReportModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form id="create-order-report-form" onsubmit="handleCreateOrderReport(event)" class="space-y-4 mt-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mã đơn hàng cần báo cáo (order_id) *</label>
                    <input type="number" id="rep-order-id" value="1" min="1" required class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeOrderReportModal()" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 font-medium">Hủy bỏ</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold shadow-sm">Tạo báo cáo đơn</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Application Logic -->
    <script>
        const AUTH_BASE_URL = 'http://127.0.0.1:8000';
        const REPORT_BASE_URL = 'http://127.0.0.1:8003';
        let currentToken = localStorage.getItem('soa_token') || '';

        function logConsole(method, url, status, data) {
            const el = document.getElementById('console-output');
            const time = new Date().toLocaleTimeString();
            const logEntry = `[${time}] ${method} ${url} -> Status: ${status}\n` + JSON.stringify(data, null, 2) + '\n\n';
            el.textContent = logEntry + el.textContent;
        }
        function clearConsole() { document.getElementById('console-output').textContent = '// Console cleared.'; }

        function switchTab(tab) {
            const prodSec = document.getElementById('tab-products-section');
            const ordSec = document.getElementById('tab-orders-section');
            const sumSec = document.getElementById('tab-summary-section');

            const prodBtn = document.getElementById('tab-products-btn');
            const ordBtn = document.getElementById('tab-orders-btn');
            const sumBtn = document.getElementById('tab-summary-btn');

            prodSec.classList.add('hidden');
            ordSec.classList.add('hidden');
            sumSec.classList.add('hidden');

            prodBtn.className = 'py-3 px-6 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2';
            ordBtn.className = 'py-3 px-6 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2';
            sumBtn.className = 'py-3 px-6 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2';

            if (tab === 'products') {
                prodSec.classList.remove('hidden');
                prodBtn.className = 'py-3 px-6 text-sm font-bold border-b-2 border-violet-600 text-violet-600 flex items-center gap-2';
                loadProductReports();
            } else if (tab === 'orders') {
                ordSec.classList.remove('hidden');
                ordBtn.className = 'py-3 px-6 text-sm font-bold border-b-2 border-emerald-600 text-emerald-600 flex items-center gap-2';
                loadOrderReports();
            } else {
                sumSec.classList.remove('hidden');
                sumBtn.className = 'py-3 px-6 text-sm font-bold border-b-2 border-purple-600 text-purple-600 flex items-center gap-2';
                loadSummaryKpi();
            }
        }

        async function loginSampleUser() {
            try {
                const b64Password = btoa('123456');
                const res = await fetch(`${AUTH_BASE_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ userName: 'student', password: b64Password })
                });
                const data = await res.json();
                logConsole('POST', `${AUTH_BASE_URL}/login`, res.status, data);

                if (res.ok && data.access_token) {
                    currentToken = data.access_token;
                    localStorage.setItem('soa_token', currentToken);
                    document.getElementById('token-display').textContent = currentToken.substring(0, 30) + '... (Hợp lệ)';
                    document.getElementById('auth-status-text').innerHTML = '<i class="fa-solid fa-circle text-[8px] text-emerald-500"></i> Đã đăng nhập JWT';
                    document.getElementById('user-info-display').textContent = 'Người dùng: student (ID: 1)';
                    loadProductReports();
                } else {
                    alert('Đăng nhập thất bại: ' + (data.message || 'Lỗi server'));
                }
            } catch (err) {
                alert('Không thể kết nối Auth Service (Port 8000): ' + err.message);
            }
        }

        function clearToken() {
            currentToken = '';
            localStorage.removeItem('soa_token');
            document.getElementById('token-display').textContent = 'Chưa có token (Vui lòng bấm Đăng nhập)';
            document.getElementById('auth-status-text').innerHTML = '<i class="fa-solid fa-circle text-[8px] text-amber-500"></i> Chờ đăng nhập';
            document.getElementById('user-info-display').textContent = 'Người dùng: Chưa xác thực';
        }

        async function loadProductReports() {
            const tbody = document.getElementById('product-reports-table-body');
            tbody.innerHTML = `<tr><td colspan="8" class="py-8 text-center text-slate-400"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang nạp báo cáo sản phẩm...</td></tr>`;

            const url = `${REPORT_BASE_URL}/reports/products`;
            try {
                const headers = { 'Accept': 'application/json' };
                if (currentToken) headers['Authorization'] = 'Bearer ' + currentToken;
                const res = await fetch(url, { headers });
                const data = await res.json();
                logConsole('GET', url, res.status, data);

                if (!res.ok) {
                    tbody.innerHTML = `<tr><td colspan="8" class="py-6 text-center text-rose-600 font-medium">${escapeHtml(data.message || 'Lỗi tải báo cáo')}</td></tr>`;
                    return;
                }

                const list = data.data || [];
                if (list.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="py-8 text-center text-slate-400">Chưa có báo cáo sản phẩm nào.</td></tr>`;
                    return;
                }

                tbody.innerHTML = list.map(r => `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-bold text-violet-700">#${r.id}</td>
                        <td class="py-3 px-4 text-slate-600">${r.order_report_id ? '#' + r.order_report_id : '<span class="text-slate-400 italic">Tổng hợp</span>'}</td>
                        <td class="py-3 px-4 text-sky-700 font-semibold">SP #${r.product_id}</td>
                        <td class="py-3 px-4 text-center font-bold">${r.total_sold}</td>
                        <td class="py-3 px-4 text-right font-semibold text-slate-900">${formatCurrency(r.revenue)}</td>
                        <td class="py-3 px-4 text-right text-amber-700">${formatCurrency(r.cost)}</td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-600">${formatCurrency(r.profit)}</td>
                        <td class="py-3 px-4 text-center">
                            <button onclick="deleteProductReport(${r.id})" class="px-2 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded text-xs">Xóa</button>
                        </td>
                    </tr>
                `).join('');
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="8" class="py-6 text-center text-rose-600">Lỗi kết nối Reporting Service: ${err.message}</td></tr>`;
            }
        }

        async function loadOrderReports() {
            const tbody = document.getElementById('order-reports-table-body');
            tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-slate-400"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang nạp báo cáo đơn hàng...</td></tr>`;

            const url = `${REPORT_BASE_URL}/reports/orders`;
            try {
                const headers = { 'Accept': 'application/json' };
                if (currentToken) headers['Authorization'] = 'Bearer ' + currentToken;
                const res = await fetch(url, { headers });
                const data = await res.json();
                logConsole('GET', url, res.status, data);

                if (!res.ok) {
                    tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-rose-600 font-medium">${escapeHtml(data.message || 'Lỗi tải báo cáo')}</td></tr>`;
                    return;
                }

                const list = data.data || [];
                if (list.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-slate-400">Chưa có báo cáo đơn hàng nào.</td></tr>`;
                    return;
                }

                tbody.innerHTML = list.map(r => `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-bold text-emerald-700">#${r.id}</td>
                        <td class="py-3 px-4 text-sky-700 font-semibold">Đơn hàng #${r.order_id}</td>
                        <td class="py-3 px-4 text-right font-semibold text-slate-900">${formatCurrency(r.total_revenue)}</td>
                        <td class="py-3 px-4 text-right text-amber-700">${formatCurrency(r.total_cost)}</td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-600">${formatCurrency(r.total_profit)}</td>
                        <td class="py-3 px-4 text-center">
                            <button onclick="deleteOrderReport(${r.id})" class="px-2 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded text-xs">Xóa</button>
                        </td>
                    </tr>
                `).join('');
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-rose-600">Lỗi kết nối Reporting Service: ${err.message}</td></tr>`;
            }
        }

        async function loadSummaryKpi() {
            const url = `${REPORT_BASE_URL}/reports/summary`;
            try {
                const headers = { 'Accept': 'application/json' };
                if (currentToken) headers['Authorization'] = 'Bearer ' + currentToken;
                const res = await fetch(url, { headers });
                const data = await res.json();
                logConsole('GET', url, res.status, data);

                if (res.ok && data.data) {
                    const s = data.data;
                    document.getElementById('kpi-inventory').textContent = Number(s.total_inventory).toLocaleString('vi-VN');
                    document.getElementById('kpi-sold').textContent = Number(s.total_sold).toLocaleString('vi-VN');
                    document.getElementById('kpi-revenue').textContent = formatCurrency(s.total_revenue);
                    document.getElementById('kpi-cost').textContent = formatCurrency(s.total_cost);
                    document.getElementById('kpi-profit').textContent = formatCurrency(s.total_profit);
                    document.getElementById('kpi-margin').textContent = s.profit_margin + '%';
                }
            } catch (err) {
                alert('Lỗi nạp KPI: ' + err.message);
            }
        }

        async function handleCreateProductReport(e) {
            e.preventDefault();
            const payload = {
                product_id: parseInt(document.getElementById('rep-product-id').value),
            };
            const ordRepId = document.getElementById('rep-order-report-id').value;
            if (ordRepId) payload.order_report_id = parseInt(ordRepId);

            const url = `${REPORT_BASE_URL}/reports/products`;
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + currentToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            logConsole('POST', url, res.status, data);

            if (res.ok) {
                alert('Tạo báo cáo sản phẩm thành công!');
                closeProductReportModal();
                loadProductReports();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể tạo báo cáo'));
            }
        }

        async function handleCreateOrderReport(e) {
            e.preventDefault();
            const payload = {
                order_id: parseInt(document.getElementById('rep-order-id').value),
            };

            const url = `${REPORT_BASE_URL}/reports/orders`;
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + currentToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            logConsole('POST', url, res.status, data);

            if (res.ok) {
                alert('Tạo báo cáo đơn hàng thành công!');
                closeOrderReportModal();
                loadOrderReports();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể tạo báo cáo'));
            }
        }

        async function deleteProductReport(id) {
            if (!confirm(`Xóa báo cáo sản phẩm #${id}?`)) return;
            const url = `${REPORT_BASE_URL}/reports/products/${id}`;
            const res = await fetch(url, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + currentToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            logConsole('DELETE', url, res.status, data);
            loadProductReports();
        }

        async function deleteOrderReport(id) {
            if (!confirm(`Xóa báo cáo đơn hàng #${id}?`)) return;
            const url = `${REPORT_BASE_URL}/reports/orders/${id}`;
            const res = await fetch(url, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + currentToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            logConsole('DELETE', url, res.status, data);
            loadOrderReports();
        }

        function openCreateProductReportModal() { document.getElementById('product-report-modal').classList.remove('hidden'); }
        function closeProductReportModal() { document.getElementById('product-report-modal').classList.add('hidden'); }
        function openCreateOrderReportModal() { document.getElementById('order-report-modal').classList.remove('hidden'); }
        function closeOrderReportModal() { document.getElementById('order-report-modal').classList.add('hidden'); }
        function formatCurrency(val) { return Number(val).toLocaleString('vi-VN') + ' ₫'; }
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m]);
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (currentToken) {
                document.getElementById('token-display').textContent = currentToken.substring(0, 30) + '... (Lưu từ trước)';
                document.getElementById('auth-status-text').innerHTML = '<i class="fa-solid fa-circle text-[8px] text-emerald-500"></i> Đã có Token';
            }
            loadProductReports();
        });
    </script>
</body>
</html>
