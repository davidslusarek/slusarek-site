<?php include('server.php'); ?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Work+Sans&display=swap" rel="stylesheet"> 

    <title>RSS - slusarek</title>
</head>

<body>
    <form method="post" action="server.php">
        <section class="main">
            <div class="first-info">
                <input type="text" class="item-url" placeholder="Insert URL" name="link">
                <input type="email" class="item-email" placeholder="Insert Email" name="email">
            </div>
            <div class="iframe-box">
                <?php
                    $result = mysqli_query($conn,"SELECT * FROM info");

                    while ($row = mysqli_fetch_array($result)) {
                        $feeds = simplexml_load_file($row["link"]);
                        if(!empty($feeds)){
        
                            $site = $feeds->channel->title;
                            $sitelink = $feeds->channel->link;
                
                            echo "<h1>".$site."</h1>";
                                foreach ($feeds->channel->item as $item) {
                
                                $title = $item->title;
                                $link = $item->link;
                                $description = $item->description;
                                $postDate = $item->pubDate;
                                $pubDate = date('D, d M Y',strtotime($postDate));
                            }
                            echo $title;
                            echo $pubDate;
                            echo implode(' ', array_slice(explode(' ', $description), 0, 20)) . "...";
                            echo "<a href='".$link."'>Czytaj więcej...</a>";
                        }
                    }
                ?>
                
            </div>

            <div class="list-box">
            
                <?php $results = mysqli_query($conn, "SELECT * FROM info"); ?>

                <table>
                    <thead>
                        <tr>
                            <th>Link</th>
                            <th>Akcja</th>
                        </tr>
                    </thead>
                    <?php while ($row = mysqli_fetch_array($results)) { ?>
                    <tr>
                        <td><?php echo $row['link']; ?></td>
                        <td>
                            <a href="server.php?del=<?php echo $row['id']; ?>" class="del_btn">Usuń</a>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
                
<?php if (isset($_SESSION['message'])): ?>
	<div class="msg">
		<?php 
			echo $_SESSION['message']; 
			unset($_SESSION['message']);
		?>
	</div>
<?php endif ?>
                
            </div>
            <div class="buttons">
                <button class="save" type="submit" name="save">Zapisz</button>
                <button class="send" type="submit" name="send">Wyślij</button>
            </div>

        </section>
    </form>
</body>

</html>