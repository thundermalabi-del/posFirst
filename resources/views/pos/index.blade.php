<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Touch friendly buttons for tablets */
        @media (max-width: 1024px) {
            button {
                min-height: 48px;
                min-width: 48px;
            }

            input,
            select {
                min-height: 48px;
            }
        }

        /* Optimize for landscape tablets */
        @media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
            .cart-section {
                width: 320px;
            }
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <livewire:p-o-s.cashier />

    @livewireScripts

    <script>
        // Auto-hide flash messages
        setTimeout(function() {
            const alerts = document.querySelectorAll('.fixed.top-4.right-4');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);

        // Keyboard shortcuts for POS
        document.addEventListener('keydown', function(e) {
            // F1 for focus search
            if (e.key === 'F1') {
                e.preventDefault();
                const searchInput = document.querySelector('input[placeholder*="Cari produk"]');
                if (searchInput) searchInput.focus();
            }

            // F2 for focus payment amount
            if (e.key === 'F2') {
                e.preventDefault();
                const paymentInput = document.querySelector('input[wire\\:model\\.live="paidAmount"]');
                if (paymentInput) paymentInput.focus();
            }

            // Enter to process transaction when payment is focused
            if (e.key === 'Enter' && e.target.matches('input[wire\\:model\\.live="paidAmount"]')) {
                const processBtn = document.querySelector('button[wire\\:click="processTransaction"]');
                if (processBtn && !processBtn.disabled) {
                    processBtn.click();
                }
            }
        });
    </script>
</body>

</html>
