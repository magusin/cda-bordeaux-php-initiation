<?php
require_once 'includes.php';

$sql = 'SELECT * FROM `user` ORDER BY `id` DESC LIMIT 5';

$users = $connection
    ->query($sql)
    ->fetchAll(PDO::FETCH_ASSOC);

// $sql2 = 'SELECT * FROM `product` ORDER BY `id`';
// $products = $connection
//     ->query($sql2)
//     ->fetchAll(PDO::FETCH_ASSOC);

// on détermine sur quel page on se trouve
if(isset($_GET['page']) && !empty($_GET['page'])){
    $currentPage = (int) strip_tags($_GET['page']);
}else{
    $currentPage = 1;
}

// On détermine le nombre total d'articles
$sql3 = 'SELECT COUNT(*) AS `id` FROM `product`';

// On prépare la requête
$query = $connection->prepare($sql3);

// On exécute
$query->execute();

// On récupère le nombre de produit
$result = $query->fetch();

$nbProduct = (int) $result['id'];

// On détermine le nombre de produit par page
$parPage = 5;

// On calcule le nombre de pages total
$pages = ceil($nbProduct / $parPage);

// Calcul du 1er article de la page
$premier = ($currentPage * $parPage) - $parPage;

$sql4 = 'SELECT * FROM `product` ORDER BY `id` DESC LIMIT :premier, :parpage;';

// On prépare la requête
$query = $connection->prepare($sql4);

$query->bindValue(':premier', $premier, PDO::PARAM_INT);
$query->bindValue(':parpage', $parPage, PDO::PARAM_INT);

// On exécute
$query->execute();

// On récupère les valeurs dans un tableau associatif
$products = $query->fetchAll(PDO::FETCH_ASSOC);
require_once 'template_head.php';
?>

<h3>Users</h3>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= $user['id'] ?></td>
        <td><?= $user['name'] ?></td>
        <td><?= $user['email'] ?></td>
        <td>
            <a href="./edit-user.php?id=<?= $user['id'] ?>">Edit user</a>
            <form method="post" action="/delete-user.php?id=<?= $user['id'] ?>">
                <button>Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    <h3>Product</h3>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Selling Price</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $product): ?>
    <tr>
        <td><?= $product['id'] ?></td>
        <td><?= $product['name'] ?></td>
        <td><?= $product['selling_price'] ?></td>
        <td>
                <a href="./detail-product.php?id=<?= $product['id'] ?>"><button>Detail product</button></a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<nav>
    <ul class="pagination">
        <!-- Lien vers la page précédente (désactivé si on se trouve sur la 1ère page) -->
            <li class="page-item <?= ($currentPage == 1) ? "disabled" : "" ?>">
                <a href="./?page=<?= $currentPage - 1 ?>" class="page-link">Précédente</a>
            </li>
            <?php for($page = 1; $page <= $pages; $page++): ?>
                <!-- Lien vers chacune des pages (activé si on se trouve sur la page correspondante) -->
            <li class="page-item <?= ($currentPage == $page) ? "active" : "" ?>">
                <a href="./?page=<?= $page ?>" class="page-link"><?= $page ?></a>
            </li>
            <?php endfor ?>
            <!-- Lien vers la page suivante (désactivé si on se trouve sur la dernière page) -->
            <li class="page-item <?= ($currentPage == $pages) ? "disabled" : "" ?>">
                <a href="./?page=<?= $currentPage + 1 ?>" class="page-link">Suivante</a>
            </li>
    </ul>
</nav>
</body>
</html>