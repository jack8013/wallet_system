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
    <h1>Wallet System</h1>
    <a href="{{ route('store') }}"><button>Create Wallet</button></a>

    <table>
        <tr>
            <th>ID</th>
            <th>Balance</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        @foreach ($wallets as $wallet)
            <tr>
                <td>{{ $wallet->id }}</td>
                <td>{{ $wallet->balance }}</td>
                <td>
                    <a href="{{ route('details', ['id' => $wallet->id, 'type' => 'deposit']) }}">
                        <button>Deposit</button>
                    </a>
                </td>
                <td>
                    <a href="{{ route('details', ['id' => $wallet->id, 'type' => 'withdrawal']) }}">
                        <button>Withdraw</button>
                    </a>
                </td>
                <td>
                <form action="{{ route('delete', $wallet->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
                </td>
            </tr>
        @endforeach
    </table>
</body>

</html>
