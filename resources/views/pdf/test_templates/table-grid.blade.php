<html lang="en">

<head>
    <title>Invoice</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

    <div class="px-2 py-8 max-w-xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <div class="text-gray-700 font-semibold text-lg">Your Company Name</div>
            </div>
            <div class="text-gray-700">
                <div class="font-bold text-xl mb-2 uppercase">Invoice</div>
                <div class="text-sm">Date: 01/05/2023</div>
                <div class="text-sm">Invoice #: 2024/00001</div>
            </div>
        </div>
        <div class="border-b-2 border-gray-300 pb-8 mb-8">
            <h2 class="text-2xl font-bold mb-4">Bill To:</h2>
            <div class="text-gray-700 mb-2">Customer Name</div>
            <div class="text-gray-700 mb-2">123 Main St.</div>
            <div class="text-gray-700 mb-2">Anytown, USA 12345</div>
            <div class="text-gray-700">johndoe@example.com</div>
        </div>
        <table class="w-full text-left mb-8">
            <thead>
                <tr>
                    <th class="text-gray-700 font-bold uppercase py-2">Description</th>
                    <th class="text-gray-700 font-bold uppercase py-2">Quantity</th>
                    <th class="text-gray-700 font-bold uppercase py-2">Price</th>
                    <th class="text-gray-700 font-bold uppercase py-2">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-4 text-gray-700">Product 1</td>
                    <td class="py-4 text-gray-700">1</td>
                    <td class="py-4 text-gray-700">$100.00</td>
                    <td class="py-4 text-gray-700">$100.00</td>
                </tr>
                <tr>
                    <td class="py-4 text-gray-700">Product 2</td>
                    <td class="py-4 text-gray-700">2</td>
                    <td class="py-4 text-gray-700">$50.00</td>
                    <td class="py-4 text-gray-700">$100.00</td>
                </tr>
                <tr>
                    <td class="py-4 text-gray-700">Product 3</td>
                    <td class="py-4 text-gray-700">3</td>
                    <td class="py-4 text-gray-700">$75.00</td>
                    <td class="py-4 text-gray-700">$225.00</td>
                </tr>
            </tbody>
        </table>
        <div class="flex justify-end mb-8">
            <div class="text-gray-700 mr-2">Subtotal:</div>
            <div class="text-gray-700">$425.00</div>
        </div>
        <div class="text-right mb-8">
            <div class="text-gray-700 mr-2">Tax:</div>
            <div class="text-gray-700">$25.50</div>

        </div>
        <div class="flex justify-end mb-8">
            <div class="text-gray-700 mr-2">Total:</div>
            <div class="text-gray-700 font-bold text-xl">$450.50</div>
        </div>
        <div class="border-t-2 border-gray-300 pt-8 mb-8">
            <div class="text-gray-700 mb-2">Payment is due within 30 days. Late payments are subject to fees.</div>
            <div class="text-gray-700 mb-2">Please make checks payable to Your Company Name and mail to:</div>
            <div class="text-gray-700">123 Main St., Anytown, USA 12345</div>
        </div>
    </div>

</body>

</html>