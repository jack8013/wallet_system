# Wallet System
Simple wallet system app that simulates creation of a wallet, deposit and withdrawal of funds with asynchronous fund rebates.

# Concurrency Handling Overview
This implementation manages concurrency using **pessimistic locking** (`lockForUpdate()`) and **polling** to ensure that updates are correctly applied after asynchronous modifications by jobs.

## 1. Pessimistic Locking with `lockForUpdate()`
- The function starts by initiating a **database transaction** to ensure atomicity.
- `lockForUpdate()` is applied on the **Wallet** model to acquire a **pessimistic lock**, preventing other operations from modifying the row until the transaction is complete.
- This ensures that **only one process** can update the wallet at a time, avoiding race conditions.
- After acquiring the lock, the function updates the **wallet balance** based on whether the operation is a **withdrawal** or **deposit**.

## 2. Transaction Creation and Job Dispatching
- A **transaction record** is created in the **Transaction** model to log the update.
- If the operation is a **deposit**, a job (`WalletJob`) is **dispatched** for asynchronous processing.
  - This job may involve additional operations such as **applying rebates** or **updating external systems**.

## 3. Polling for Rebate Application
- After the transaction, **polling** is used to check if the rebate has been applied asynchronously.
- The loop runs for a **defined timeout** (e.g., **3 seconds**), repeatedly checking if the **rebate_amount** field in the wallet has changed.
- If a rebate is detected, the **wallet data is updated**, and the loop exits early.

## 4. Timeout Mechanism
- The polling process is **time-limited** to prevent the system from waiting indefinitely.
- Once the timeout is reached, the function **returns the latest available wallet and transaction data**.

## How to use
Set up your Laravel environment as usual, run `composer install` for installing dependencies.

#
# Wallet API Documentation

## **Relevant APIs**

### **Create a New Wallet**
**Endpoint:** `POST /wallets`

**Description:**
Creates a new wallet with a randomized user ID and a balance of 0.

**Request Body:**
_None_

**Response:**
```json
{
  "data": {
    "id": 3,
    "user_id": 691,
    "balance": 0
  }
}
```

---

### **Get Wallet by ID**
**Endpoint:** `GET /wallets/{id}`

**Description:**
Retrieves details of a single wallet.

**Parameters:**
- `id` (integer, required) – The wallet's ID

**Response:**
```json
{
  "id": 1,
  "user_id": 691,
  "balance": 0
}
```

---

### **Deposit Funds**
**Endpoint:** `POST /wallets/{id}/deposit`

**Description:**
Adds funds to a wallet.

**Parameters:**
- `id` (integer, required) – The wallet's ID

**Request Body:**
```json
{
  "amount": 50.00
}
```

**Response:**
```json
{
  "wallet": {
    "id": 3,
    "user_id": 691,
    "balance": "50.50"
  },
  "transaction": {
    "id": 33,
    "wallet_id": "3",
    "amount": 50,
    "type": "deposit"
  }
}
```

---

### **Withdraw Funds**
**Endpoint:** `POST /wallets/{id}/withdraw`

**Description:**
Withdraws funds from a wallet.

**Parameters:**
- `id` (integer, required) – The wallet's ID

**Request Body:**
```json
{
  "amount": 50.00
}
```

**Response:**
```json
{
  "wallet": {
    "id": 3,
    "user_id": 691,
    "balance": "151.60"
  },
  "transaction": {
    "id": 35,
    "wallet_id": "3",
    "amount": 10,
    "type": "withdrawal"
  }
}
```

---

### **Get Wallet Transactions**
**Endpoint:** `GET /wallets/{id}/transactions`

**Description:**
Retrieves all transactions for a wallet.

**Response:**
```json
{
  "wallet": {
    "id": 4,
    "user_id": 302,
    "balance": "51.00"
  },
  "transaction": {
    "current_page": 1,
    "data": [
      {
        "id": 36,
        "wallet_id": 4,
        "type": "deposit",
        "amount": "100.00"
      },
      {
        "id": 37,
        "wallet_id": 4,
        "type": "rebate",
        "amount": "1.00"
      },
      {
        "id": 38,
        "wallet_id": 4,
        "type": "withdrawal",
        "amount": "50.00"
      }
    ]
  }
}
```


