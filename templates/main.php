    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий (задание после второй лекции)-->
            <?php
            foreach($categories as $value): ?>
                <li class="promo__item promo__item--<?=$value['symbol_code']; ?>">
                    <a class="promo__link" href="pages/all-lots.html"><?=htmlspecialchars($value['category_name']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <?php foreach($goods as $good): ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="<?=$good['img']; ?>" width="350" height="260" alt="">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?=htmlspecialchars($good['category_name']); ?></span>
                        <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?=htmlspecialchars($good['lot_name']); ?></a></h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount"></span>
                                <span class="lot__cost"><?= format_as_price_in_rub($good['initial_price']); ?></span>
                            </div>
                            <?php $timer = timer($good['dt_end']); ?>
                            <div class="lot__timer timer <?php if ($timer[0] < 1): ?> timer--finishing<?php endif;?>">
                                <?= $timer[0]?>:<?=$timer[1]; ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
