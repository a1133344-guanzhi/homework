<html>
<head>
    <title>我的購物車</title>
    <style>
        table { width: 80%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #999; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #eee; }
        .btn-delete { color: white; background-color: red; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
    </style>
</head>
<body>
    <h2>我的購物車清單</h2>
    <hr>
    
    <table>
        <thead>
            <tr>
                <th>編號</th>
                <th>商品名稱</th>
                <th>單價</th>
                <th>數量</th>
                <th>小計</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_all = 0;
            $found_item = false;

            foreach ($_COOKIE as $id => $data) {
                if (is_array($data) && isset($data["ID"], $data["Price"], $data["Quantity"])) {
                    $found_item = true;
                    $item_id = htmlspecialchars($data["ID"]);
                    $name = htmlspecialchars($data["Name"]);
                    $price = (int)$data["Price"];
                    $quantity = (int)$data["Quantity"];
                    $subtotal = $price * $quantity;
                    $total_all += $subtotal;

                    echo "<tr>";
                    echo "<td>{$item_id}</td>";
                    echo "<td>{$name}</td>";
                    echo "<td>$" . number_format($price) . "</td>";
                    echo "<td>{$quantity}</td>";
                    echo "<td>$" . number_format($subtotal) . "</td>";
                    // 連結至修正後的 delete.php
                    echo "<td><a href='delete.php?Id={$item_id}' class='btn-delete'>刪除</a></td>";
                    echo "</tr>";
                }
            }

            if (!$found_item) {
                echo "<tr><td colspan='6'>購物車目前是空的。</td></tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">總計金額：</td>
                <td colspan="2">$<?php echo number_format($total_all); ?></td>
            </tr>
        </tfoot>
    </table>

    <hr>
    <a href="catalog.php">繼續購物</a>
</body>
</html>