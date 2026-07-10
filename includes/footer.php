    <!-- Футер -->
    <footer style="text-align: center; padding: 40px 0; border-top: 1px solid rgba(255,255,255,0.05); color: rgba(255,255,255,0.3); font-size: 14px; margin-top: 40px;">
        <p>
            <a href="/" style="color: rgba(255,255,255,0.5); text-decoration: none; margin: 0 10px;">Главная</a>
            <a href="/blog.php">Блог</a>
            <a href="/cases.php">Кейсы</a>
            <a href="/agents.php" style="color: rgba(255,255,255,0.5); text-decoration: none; margin: 0 10px;">Агенты</a>
            <a href="/login_page.php" style="color: rgba(255,255,255,0.5); text-decoration: none; margin: 0 10px;">Вход</a>
            <a href="/register_page.php" style="color: rgba(255,255,255,0.5); text-decoration: none; margin: 0 10px;">Регистрация</a>
        </p>
        <p>© 2026 AI Agent Pro. Разработан в России.</p>
        <p>Индивидуальный предприниматель Озерова О.Е. ИНН:422105721871 ОГРН:326420500069203 </p>
    </footer>
</div>
<!-- Yandex Autoplacement 19571269 -->
<script src="https://yandex.ru/ads/system/context.js" async></script>
<script data-page-id="19571269" src="https://yandex.ru/ads/system/ap-loader.js" async></script>
<!-- Yandex.RTB R-A-19575210-1 -->
<script>
window.yaContextCb.push(() => {
    Ya.Context.AdvManager.render({
        "blockId": "R-A-19575210-1",
        "type": "fullscreen",
        "platform": "touch"
    })
})
</script>
</div>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function(m,e,t,r,i,k,a){
        m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
    })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=110551292', 'ym');

    ym(110551292, 'init', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/110551292" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

</body>

<?php
// === БАННЕРЫ ===
require_once __DIR__ . '/banners.php';
echo showBanners('footer');
?>

</html>
