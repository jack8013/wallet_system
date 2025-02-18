<!DOCTYPE html>
<html lang="en">

<style>
    table {
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <div class="m-3">
        <h1>{{ $type == 'deposit' ? 'Deposit Funds' : 'Withdraw Funds' }}</h1>
        <h2>Wallet ID: {{ $wallet->id }}</h2>
        <h2>Balance: {{ $wallet->balance }}</h2>

        <form action="{{ $type == 'deposit' ? route('deposit', $wallet->id) : route('withdraw', $wallet->id) }}"
            method="post">
            @csrf
            <input type="number" placeholder="0.00" name="amount" min="{{ $type == 'deposit' ? '1' : '0.01' }}"
                step="0.01" required>
            <input type="submit" value="{{ $type == 'deposit' ? 'Deposit' : 'Withdraw' }}">
        </form>

        @if (session('success'))
            <p class="text-green-500">{{ session('success') }}</p>
        @endif

        @if (session('error'))
            <p class="text-red-500">{{ session('error') }}</p>
        @endif

        <h2>Transaction History</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Date</th>
            </tr>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->amount }}</td>
                    <td>{{ $transaction->type }}</td>
                    <td>{{ $transaction->created_at }}</td>
            @endforeach
        </table>

        <div class="links">
            {{ $transactions->links() }}
        </div>

        <a href="/"><button>Back</button></a>
    </div>
</body>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</html>
