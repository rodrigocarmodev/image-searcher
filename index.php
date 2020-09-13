<?php

require("./src/config/access_key.php");

$search = $_GET["search"] ? trim($_GET["search"]) : false;
$page = $_GET["page"] ? (int)$_GET["page"] : 1;

if ($page < 100) {
    $blockPageMargin = "margin: 0px 8px;";
} elseif ($page >= 100 && $page < 1000) {
    $blockPageMargin = "margin: 0px 17px;";
} else {
    $blockPageMargin = "margin: 0px 20px;";
}

if ($search && !empty($search)) {
    $curl = curl_init();
    $url = "https://api.unsplash.com/search/photos?page=$page&per_page=9&query=" . urlencode($search) . "&client_id=" . urlencode($access_key);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //set to return string
    $images = json_decode(curl_exec($curl));
    curl_close($curl);
} else {
    $curl = curl_init();
    $url = "https://api.unsplash.com/photos/random?count=5&client_id=" . urlencode($access_key);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //set to return string
    $randomImages = json_decode(curl_exec($curl));
    curl_close($curl);
}

$totalPages = $images->total_pages ? (int)$images->total_pages : 0;

if ($totalPages !== 0 && $page > $totalPages) {
    header("Location: http://localhost:8888/image-searcher/?page=$totalPages&search=$search");
    exit();
}

if ($page > 1) {
    if ($page == $totalPages) {
        $nextPage = $page;
        $finalPage = true;
        $page--;
        $prevPage = $page - 1;
    } else {
        $prevPage = $page - 1;
        $nextPage = $page + 1;
    }
} else {
    $prevPage = $page;
    $page++;
    $firstPage = true;
    $nextPage = $page + 1;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Searcher by Rodrigo Carmo :]</title>
    <link rel="stylesheet" href="./src/css/main.css">
    <link rel="stylesheet" href="./src/css/font-awesome-4.7.0/css/font-awesome.min.css">

    <style>
        @font-face {
            font-family: 'Caveat';
            src: url('./src/fonts/Caveat/Caveat-Regular.ttf');
        }
    </style>
</head>

<body onkeydown="pressedKey(event)">
    <div class="div-search">
        <h1><a href="http://localhost:8888/image-searcher/" style="font-family: 'Caveat'; font-size: 45px;">See and be inspired by, let's explore it!</a></h1>
        <form>
            <input type="text" name="search" class="form-search-input">
            <button class="btn-search" style="cursor: pointer;"><i class="fa fa-search" aria-hidden="true"></i>
            </button>
        </form>
    </div>
    <?php if ($search && !empty($search)) { ?>
        <div class="center" style="font-size: 20px; margin: 15px auto;">Showing results for: "<strong><?= $search ?></strong>"</div>
        <div class="card">
            <?php for ($i = 0; $i < 9; $i++) {
                $description = $images->results[$i]->alt_description;
                $image = $images->results[$i]->urls->regular;
                $imageFullsize[$i] = $images->results[$i]->urls->full; ?>
                <div class="card-img" onclick="showFullsizeImage('<?= $imageFullsize[$i] ?>')" id="<?= $i ?>">
                    <img id="image-loading-<?= $i ?>" src="./src/img/loading.gif" alt="" style="display: block">
                    <img id="main-img-<?= $i ?>" onload="showImage(<?= $i ?>)" src="<?= $image ?>" style="max-width:100%; max-height:100%; display: none;" alt="<?= $description ?>"></div>
            <?php } ?>
        </div>
        <div class="div-page">
            <div class="block-page" style="<?= $blockPageMargin ?>">
                <form>
                    <input type="hidden" name="page" value="<?= $prevPage ?>">
                    <input type="hidden" name="search" value="<?= $search ?>">
                    <input type="submit" class="block-page-input <?php if ($firstPage) {
                                                                        echo 'page-btn-active';
                                                                    } else {
                                                                        echo 'page-btn';
                                                                    } ?>" value="<?= $prevPage ?>">
                </form>
            </div>
            <div class="block-page" style="<?= $blockPageMargin ?>">
                <form>
                    <input type="hidden" name="page" value="<?= $page ?>">
                    <input type="hidden" name="search" value="<?= $search ?>">
                    <input type="submit" class="block-page-input <?php if ($page == $page && !$firstPage && !$finalPage) {
                                                                        echo 'page-btn-active';
                                                                    } else {
                                                                        echo 'page-btn';
                                                                    } ?>" value="<?= $page ?>">
                </form>
            </div>
            <div class="block-page" style="<?= $blockPageMargin ?>">
                <form>
                    <input type="hidden" name="page" value="<?= $nextPage ?>">
                    <input type="hidden" name="search" value="<?= $search ?>">
                    <input type="submit" class="block-page-input <?php if ($finalPage) {
                                                                        echo 'page-btn-active';
                                                                    } else {
                                                                        echo 'page-btn';
                                                                    } ?>" value="<?= $nextPage ?>">
                </form>
            </div>
            <div class="search-page"><span style="margin: 0px 5px; font-size: 17px;">Go to </span>
                <form>
                    <input type="number" name="page">
                    <input type="hidden" name="search" value="<?= $search ?>">
                </form>
            </div>
        </div>
    <?php } else { ?>

        <div class="card" style="margin-top: 90px;">
            <?php for ($i = 0; $i < 5; $i++) {
                $description = $randomImages[$i]->alt_description;
                $image = $randomImages[$i]->urls->regular;
                $imageFullsize[$i] = $randomImages[$i]->urls->full; ?>
                <div class="card-img" style="width: 235px !important;" onclick="showFullsizeImage('<?= $imageFullsize[$i] ?>')" id="<?= $i ?>">
                    <img id="image-loading-<?= $i ?>" src="./src/img/loading.gif" alt="" style="display: block">
                    <img id="main-img-<?= $i ?>" src="<?= $image ?>" style="max-width:100%; max-height:100%; display: none;" alt="<?= $description ?>" onload="showImage(<?= $i ?>)"></div>
            <?php } ?>
        </div>

    <?php } ?>


    <div class="img-fullsize" id="img-fullsize" style="display: none;">
        <button class="btn-close-image" id="btn-close-image"><i class="fa fa-times" aria-hidden="true"></i>
        </button>
        <button class="btn-next-image" id="btn-next-image"><i class="fa fa-chevron-right" aria-hidden="true"></i>
        </button>
        <button class="btn-prev-image" id="btn-prev-image"><i class="fa fa-chevron-left" aria-hidden="true"></i>
        </button> <a href="" target="_blank"><img src="" alt="img fullsize"></a>
        <span id="loading-message"><img src="src/img/loading.gif" style="height: 80px; width: 80px;"></span>
    </div>

    <script>
        const closeFullsizeImage = () => {
            let divFullsizeImage = document.getElementById("img-fullsize");
            divFullsizeImage.style.display = "none";
            let body = document.querySelector('body');
            body.style.overflow = "visible";
        }

        const showFullsizeImage = (urlImage) => {
            let divFullsizeImage = document.getElementById("img-fullsize");
            let image = document.querySelector(".img-fullsize img");
            let imageLink = document.querySelector(".img-fullsize a");
            image.src = urlImage;
            imageLink.href = urlImage;
            let loadingMessage = document.getElementById("loading-message");
            image.onload = () => loadingMessage.style.display = "none";
            divFullsizeImage.style.top = `${document.documentElement.scrollTop}px`;
            let body = document.querySelector('body');
            body.style.overflow = "hidden";
            divFullsizeImage.style.display = "block";
        };

        const images = <?= json_encode($imageFullsize) ?>;

        const showPrevImage = () => {
            let image = document.querySelector("#img-fullsize img");
            let loadingMessage = document.getElementById("loading-message");
            image.style.display = "none";
            loadingMessage.style.display = "flex";
            let id_image = parseInt(image["id"]) || 0;
            let prevImage = id_image > 0 ? id_image - 1 : 0;
            image.src = images[prevImage];
            image.id = id_image > 0 ? id_image - 1 : 0;
            image.onload = () => {
                loadingMessage.style.display = "none";
                image.style.display = "block";
            };
        }

        const showNextImage = () => {
            let image = document.querySelector("#img-fullsize img");
            let loadingMessage = document.getElementById("loading-message");
            image.style.display = "none";
            loadingMessage.style.display = "flex";
            let id_image = parseInt(image["id"]) || 0;
            let nextImage = id_image < images.length - 1 ? id_image + 1 : images.length - 1;
            image.src = images[nextImage];
            image.id = id_image + 1;
            image.onload = () => {
                loadingMessage.style.display = "none";
                image.style.display = "block";
            };
        }

        const showImage = (id_image) => {
            let image = document.querySelector(`.card .card-img #main-img-${id_image}`);
            let loadingMessage = document.querySelector(`.card .card-img #image-loading-${id_image}`);
            loadingMessage.style.display = "none";
            image.style.display = "block";
        };

        function pressedKey(event) {
            const key = event.keyCode;

            switch (key) {
                case 37:
                    showPrevImage();
                    break;

                case 39:
                    showNextImage();
                    break;

                case 27:
                    closeFullsizeImage();
                    break;
            }
        }

        const btnPrevImage = document.getElementById('btn-prev-image');
        btnPrevImage.onclick = showPrevImage;

        const btnNextImage = document.getElementById('btn-next-image');
        btnNextImage.onclick = showNextImage;

        const btnClose = document.getElementById("btn-close-image");
        btnClose.onclick = closeFullsizeImage;
    </script>
</body>

</html>