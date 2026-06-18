<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Restoran\StatusMenu;
use App\Models\Restoran\StatusPesanan;
use App\Models\Restoran\KategoriMenu;
use App\Models\Restoran\Menu;
use App\Models\Hotel\Promo;

class RestoranSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('TRUNCATE TABLE ulasan_restoran RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE detail_pesanan RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE pesanan_menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE event_menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE kategori_menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE status_menu RESTART IDENTITY CASCADE');
        DB::statement('TRUNCATE TABLE status_pesanan RESTART IDENTITY CASCADE');

        $tersedia = StatusMenu::create(['id' => 1, 'nama_status' => 'Tersedia']);
        $habis    = StatusMenu::create(['id' => 2, 'nama_status' => 'Habis']);

        StatusPesanan::create(['id' => 1, 'nama_status' => 'MENUNGGU']);
        StatusPesanan::create(['id' => 2, 'nama_status' => 'DIMASAK']);
        StatusPesanan::create(['id' => 3, 'nama_status' => 'DISAJIKAN']);
        StatusPesanan::create(['id' => 4, 'nama_status' => 'SELESAI']);

        $categories = [
            'Best Sellers'   => 'Menu favorit dan paling sering dipesan pelanggan.',
            'Mie Goreng'     => 'Aneka mie goreng dengan pilihan mie kuning, kwetiau, atau bihun.',
            'Nasi Goreng'    => 'Pilihan nasi goreng khas dengan berbagai varian topping.',
            'Soup'           => 'Sup hangat dengan pilihan daging dan seafood.',
            'Pasta'          => 'Hidangan pasta ala western dengan saus pilihan.',
            'Rice Bowls'     => 'Nasi dengan topping praktis dan cita rasa modern.',
            'Sayur'          => 'Aneka menu sayuran dan masakan tradisional Indonesia.',
            'Dessert'        => 'Hidangan penutup manis untuk melengkapi santapan.',
            'Snacks'         => 'Camilan dan makanan ringan untuk teman bersantai.',
            'Espresso Based' => 'Minuman kopi berbasis espresso.',
            'Non Coffee'     => 'Minuman non kopi berbasis susu dan bubuk premium.',
            'Frappe'         => 'Minuman dingin blended dengan tekstur creamy.',
            'Tea'            => 'Aneka minuman teh panas maupun dingin.',
            'Fresh & Fruity' => 'Jus buah segar tanpa alkohol.',
            'Mocktails'      => 'Minuman segar non alkohol dengan kombinasi buah dan soda.',
            'Smoothies'      => 'Minuman buah blended yang creamy dan menyegarkan.',
            'Tea Pot'        => 'Sajian teh premium dalam teko untuk dinikmati bersama.',
            'Others'         => 'Minuman kemasan dan air mineral.',
        ];

        $cats = [];
        foreach ($categories as $nama => $desc) {
            $cats[$nama] = KategoriMenu::create(['nama_kategori' => $nama]);
        }

        $menus = [
            // Best Sellers
            ['nama_menu' => 'Nasi Ayam Geprek', 'harga' => 40000, 'kategori' => 'Best Sellers', 'deskripsi' => 'Ayam goreng crispy dengan sambal geprek pedas dan nasi putih.'],
            ['nama_menu' => 'Ikan Nila Goreng', 'harga' => 50000, 'kategori' => 'Best Sellers', 'deskripsi' => 'Ikan nila goreng renyah disajikan dengan nasi dan sambal.'],
            ['nama_menu' => 'Nasi Ayam Bakar', 'harga' => 40000, 'kategori' => 'Best Sellers', 'deskripsi' => 'Ayam bakar berbumbu khas Indonesia dengan nasi putih.'],
            ['nama_menu' => 'Nasi Bebek Cabe Hijau', 'harga' => 40000, 'kategori' => 'Best Sellers', 'deskripsi' => 'Bebek goreng dengan sambal cabai hijau dan nasi putih.'],
            // Mie Goreng
            ['nama_menu' => 'Mie Goreng Seafood', 'harga' => 45000, 'kategori' => 'Mie Goreng', 'deskripsi' => 'Mie goreng dengan campuran seafood dan sayuran segar.'],
            ['nama_menu' => 'Mie Goreng Ayam', 'harga' => 40000, 'kategori' => 'Mie Goreng', 'deskripsi' => 'Mie goreng dengan potongan ayam berbumbu spesial.'],
            ['nama_menu' => 'Mie Goreng Telur', 'harga' => 35000, 'kategori' => 'Mie Goreng', 'deskripsi' => 'Mie goreng sederhana dengan telur dan bumbu khas.'],
            // Nasi Goreng
            ['nama_menu' => 'Nasi Goreng Purnama', 'harga' => 43000, 'kategori' => 'Nasi Goreng', 'deskripsi' => 'Nasi goreng signature khas Purnama dengan bumbu spesial.'],
            ['nama_menu' => 'Nasi Goreng Seafood', 'harga' => 43000, 'kategori' => 'Nasi Goreng', 'deskripsi' => 'Nasi goreng dengan aneka seafood pilihan.'],
            ['nama_menu' => 'Nasi Goreng Kampung', 'harga' => 35000, 'kategori' => 'Nasi Goreng', 'deskripsi' => 'Nasi goreng tradisional Indonesia dengan cita rasa autentik.'],
            ['nama_menu' => 'Nasi Goreng Telur', 'harga' => 35000, 'kategori' => 'Nasi Goreng', 'deskripsi' => 'Nasi goreng sederhana dengan telur dan bumbu khas.'],
            // Soup
            ['nama_menu' => 'Soup Buntut', 'harga' => 65000, 'kategori' => 'Soup', 'deskripsi' => 'Sup buntut sapi dengan kuah gurih dan rempah pilihan.'],
            ['nama_menu' => 'Soup Iga', 'harga' => 65000, 'kategori' => 'Soup', 'deskripsi' => 'Sup iga sapi dengan kuah kaldu yang kaya rasa.'],
            ['nama_menu' => 'Soup Kerbau', 'harga' => 37000, 'kategori' => 'Soup', 'deskripsi' => 'Sup daging kerbau dengan cita rasa tradisional.'],
            // Pasta
            ['nama_menu' => 'Creamy Mushroom Pasta', 'harga' => 45000, 'kategori' => 'Pasta', 'deskripsi' => 'Pasta creamy dengan saus jamur yang lembut dan gurih.'],
            ['nama_menu' => 'Aglio Olio', 'harga' => 40000, 'kategori' => 'Pasta', 'deskripsi' => 'Pasta tumis bawang putih dan minyak zaitun khas Italia.'],
            // Rice Bowls
            ['nama_menu' => 'Chicken Katsu Rice Bowl', 'harga' => 40000, 'kategori' => 'Rice Bowls', 'deskripsi' => 'Chicken katsu crispy dengan nasi hangat dan saus spesial.'],
            ['nama_menu' => 'Dory Sambal Matah Rice Bowl', 'harga' => 40000, 'kategori' => 'Rice Bowls', 'deskripsi' => 'Ikan dory crispy dengan sambal matah khas Bali.'],
            ['nama_menu' => 'Black Pepper Beef Rice Bowl', 'harga' => 45000, 'kategori' => 'Rice Bowls', 'deskripsi' => 'Daging sapi lada hitam dengan nasi putih hangat.'],
            ['nama_menu' => 'Black Pepper Chicken Rice Bowl', 'harga' => 40000, 'kategori' => 'Rice Bowls', 'deskripsi' => 'Ayam lada hitam dengan saus gurih dan nasi putih.'],
            // Sayur
            ['nama_menu' => 'Broccoli Bawang Putih', 'harga' => 30000, 'kategori' => 'Sayur', 'deskripsi' => 'Brokoli tumis bawang putih yang sehat dan lezat.'],
            ['nama_menu' => 'Tumis Kangkung Terasi', 'harga' => 25000, 'kategori' => 'Sayur', 'deskripsi' => 'Kangkung tumis dengan terasi khas Indonesia.'],
            ['nama_menu' => 'Sopo Tahu', 'harga' => 40000, 'kategori' => 'Sayur', 'deskripsi' => 'Sup tahu dengan sayuran dan kuah gurih.'],
            ['nama_menu' => 'Cap Cay', 'harga' => 35000, 'kategori' => 'Sayur', 'deskripsi' => 'Aneka sayuran tumis dengan saus spesial.'],
            ['nama_menu' => 'Gado-Gado', 'harga' => 30000, 'kategori' => 'Sayur', 'deskripsi' => 'Sayuran segar dengan saus kacang khas Indonesia.'],
            ['nama_menu' => 'Sayur Urap', 'harga' => 30000, 'kategori' => 'Sayur', 'deskripsi' => 'Sayuran rebus dengan kelapa parut berbumbu.'],
            // Dessert
            ['nama_menu' => 'Cheese Cake', 'harga' => 42000, 'kategori' => 'Dessert', 'deskripsi' => 'Kue keju lembut dengan rasa creamy yang nikmat.'],
            ['nama_menu' => 'Cookies', 'harga' => 14000, 'kategori' => 'Dessert', 'deskripsi' => 'Kue kering renyah sebagai teman minum kopi atau teh.'],
            // Snacks
            ['nama_menu' => 'Calamari', 'harga' => 35000, 'kategori' => 'Snacks', 'deskripsi' => 'Cumi goreng tepung crispy dengan saus pendamping.'],
            ['nama_menu' => 'Ubi Goreng', 'harga' => 25000, 'kategori' => 'Snacks', 'deskripsi' => 'Ubi goreng renyah dan manis alami.'],
            ['nama_menu' => 'Kentang Goreng', 'harga' => 25000, 'kategori' => 'Snacks', 'deskripsi' => 'French fries crispy dengan saus tomat dan sambal.'],
            ['nama_menu' => 'Chicken Wings', 'harga' => 35000, 'kategori' => 'Snacks', 'deskripsi' => 'Sayap ayam goreng berbumbu gurih.'],
            ['nama_menu' => 'BBQ Wings', 'harga' => 40000, 'kategori' => 'Snacks', 'deskripsi' => 'Sayap ayam dengan saus barbeque khas.'],
            ['nama_menu' => 'Chicken Nugget', 'harga' => 28000, 'kategori' => 'Snacks', 'deskripsi' => 'Nugget ayam crispy favorit semua usia.'],
            ['nama_menu' => 'Spring Roll', 'harga' => 26000, 'kategori' => 'Snacks', 'deskripsi' => 'Lumpia goreng renyah isi sayuran.'],
            ['nama_menu' => 'Popcorn Chicken', 'harga' => 30000, 'kategori' => 'Snacks', 'deskripsi' => 'Potongan ayam crispy ukuran bite-size.'],
            ['nama_menu' => 'Pisang Goreng', 'harga' => 30000, 'kategori' => 'Snacks', 'deskripsi' => 'Pisang goreng hangat dengan tekstur renyah.'],
            ['nama_menu' => 'Fish & Chips', 'harga' => 40000, 'kategori' => 'Snacks', 'deskripsi' => 'Ikan goreng tepung dengan kentang goreng.'],
            // Espresso Based
            ['nama_menu' => 'Espresso', 'harga' => 18000, 'kategori' => 'Espresso Based', 'deskripsi' => 'Kopi espresso murni dengan rasa kuat dan pekat.'],
            ['nama_menu' => 'Americano Hot', 'harga' => 20000, 'kategori' => 'Espresso Based', 'deskripsi' => 'Espresso dengan tambahan air panas.'],
            ['nama_menu' => 'Americano Iced', 'harga' => 20000, 'kategori' => 'Espresso Based', 'deskripsi' => 'Espresso dengan tambahan air dingin dan es.'],
            ['nama_menu' => 'Long Black Hot', 'harga' => 20000, 'kategori' => 'Espresso Based', 'deskripsi' => 'Espresso double shot dengan air panas.'],
            ['nama_menu' => 'Long Black Iced', 'harga' => 22000, 'kategori' => 'Espresso Based', 'deskripsi' => 'Espresso double shot dengan es.'],
            ['nama_menu' => 'Sanger Hot', 'harga' => 23000, 'kategori' => 'Espresso Based', 'deskripsi' => 'Kopi khas Aceh dengan susu kental manis panas.'],
            ['nama_menu' => 'Sanger Iced', 'harga' => 25000, 'kategori' => 'Espresso Based', 'deskripsi' => 'Kopi khas Aceh dengan susu kental manis dingin.'],
            ['nama_menu' => 'Piccolo Hot', 'harga' => 28000, 'kategori' => 'Espresso Based', 'deskripsi' => 'Espresso dengan sedikit susu steam.'],
            ['nama_menu' => 'Coconut Americano Iced', 'harga' => 30000, 'kategori' => 'Espresso Based', 'deskripsi' => 'Americano dengan santan kelapa segar.'],
            ['nama_menu' => 'Peach Coffee Iced', 'harga' => 32000, 'kategori' => 'Espresso Based', 'deskripsi' => 'Kopi dingin dengan sirup persik yang segar.'],
            // Non Coffee - Latte
            ['nama_menu' => 'Cafe Latte Hot', 'harga' => 28000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Espresso dengan susu steam panas.'],
            ['nama_menu' => 'Cafe Latte Iced', 'harga' => 30000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Espresso dengan susu dingin dan es.'],
            ['nama_menu' => 'Gula Aren Latte Hot', 'harga' => 33000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan gula aren asli yang manis alami panas.'],
            ['nama_menu' => 'Gula Aren Latte Iced', 'harga' => 35000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan gula aren asli dingin.'],
            ['nama_menu' => 'Hazelnut Latte Hot', 'harga' => 33000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan sirup hazelnut panas.'],
            ['nama_menu' => 'Hazelnut Latte Iced', 'harga' => 35000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan sirup hazelnut dingin.'],
            ['nama_menu' => 'Tiramisu Latte Hot', 'harga' => 33000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan cita rasa tiramisu panas.'],
            ['nama_menu' => 'Tiramisu Latte Iced', 'harga' => 35000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan cita rasa tiramisu dingin.'],
            ['nama_menu' => 'Caramel Latte Hot', 'harga' => 33000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan saus karamel panas.'],
            ['nama_menu' => 'Caramel Latte Iced', 'harga' => 35000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan saus karamel dingin.'],
            ['nama_menu' => 'Vanilla Latte Hot', 'harga' => 33000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan sirup vanilla panas.'],
            ['nama_menu' => 'Vanilla Latte Iced', 'harga' => 35000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan sirup vanilla dingin.'],
            ['nama_menu' => 'Mochachino Hot', 'harga' => 35000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan coklat mocha panas.'],
            ['nama_menu' => 'Mochachino Iced', 'harga' => 37000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Latte dengan coklat mocha dingin.'],
            ['nama_menu' => 'Affogato', 'harga' => 40000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Espresso panas dituang di atas es krim vanilla.'],
            ['nama_menu' => 'Milo Hot', 'harga' => 17000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Minuman milo hangat klasik.'],
            ['nama_menu' => 'Milo Iced', 'harga' => 19000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Minuman milo dingin dengan es.'],
            ['nama_menu' => 'Taro Hot', 'harga' => 30000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Minuman taro hangat dengan susu.'],
            ['nama_menu' => 'Taro Iced', 'harga' => 32000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Minuman taro dingin dengan susu dan es.'],
            ['nama_menu' => 'Matcha Hot', 'harga' => 30000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Matcha latte hangat premium.'],
            ['nama_menu' => 'Matcha Iced', 'harga' => 32000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Matcha latte dingin premium.'],
            ['nama_menu' => 'Red Velvet Hot', 'harga' => 30000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Minuman red velvet hangat dengan susu.'],
            ['nama_menu' => 'Red Velvet Iced', 'harga' => 32000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Minuman red velvet dingin dengan susu dan es.'],
            ['nama_menu' => 'Choco Delight Hot', 'harga' => 30000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Minuman coklat hangat yang lezat.'],
            ['nama_menu' => 'Choco Delight Iced', 'harga' => 32000, 'kategori' => 'Non Coffee', 'deskripsi' => 'Minuman coklat dingin yang menyegarkan.'],
            // Frappe
            ['nama_menu' => 'Taro Frappe', 'harga' => 35000, 'kategori' => 'Frappe', 'deskripsi' => 'Frappe taro creamy dan manis.'],
            ['nama_menu' => 'Matcha Frappe', 'harga' => 39000, 'kategori' => 'Frappe', 'deskripsi' => 'Frappe matcha premium yang menyegarkan.'],
            ['nama_menu' => 'Cookies n Cream Frappe', 'harga' => 39000, 'kategori' => 'Frappe', 'deskripsi' => 'Frappe dengan oreo dan krim lembut.'],
            ['nama_menu' => 'Chocolate Frappe', 'harga' => 35000, 'kategori' => 'Frappe', 'deskripsi' => 'Frappe coklat creamy dan dingin.'],
            // Tea
            ['nama_menu' => 'Lemon Tea Hot', 'harga' => 17000, 'kategori' => 'Tea', 'deskripsi' => 'Teh lemon hangat yang menyegarkan.'],
            ['nama_menu' => 'Lemon Tea Iced', 'harga' => 19000, 'kategori' => 'Tea', 'deskripsi' => 'Teh lemon dingin yang menyegarkan.'],
            ['nama_menu' => 'Lychee Tea Hot', 'harga' => 17000, 'kategori' => 'Tea', 'deskripsi' => 'Teh leci hangat dengan aroma buah.'],
            ['nama_menu' => 'Lychee Tea Iced', 'harga' => 19000, 'kategori' => 'Tea', 'deskripsi' => 'Teh leci dingin dengan aroma buah.'],
            ['nama_menu' => 'Teh Tarik Hot', 'harga' => 17000, 'kategori' => 'Tea', 'deskripsi' => 'Teh tarik khas Malaysia panas.'],
            ['nama_menu' => 'Teh Tarik Iced', 'harga' => 19000, 'kategori' => 'Tea', 'deskripsi' => 'Teh tarik khas Malaysia dingin.'],
            ['nama_menu' => 'Sweet Tea Hot', 'harga' => 10000, 'kategori' => 'Tea', 'deskripsi' => 'Teh manis hangat klasik.'],
            ['nama_menu' => 'Sweet Tea Iced', 'harga' => 12000, 'kategori' => 'Tea', 'deskripsi' => 'Teh manis dingin klasik.'],
            ['nama_menu' => 'Unsweet Tea Hot', 'harga' => 8000, 'kategori' => 'Tea', 'deskripsi' => 'Teh tawar hangat tanpa gula.'],
            ['nama_menu' => 'Unsweet Tea Iced', 'harga' => 10000, 'kategori' => 'Tea', 'deskripsi' => 'Teh tawar dingin tanpa gula.'],
            // Fresh & Fruity
            ['nama_menu' => 'Watermelon Juice', 'harga' => 23000, 'kategori' => 'Fresh & Fruity', 'deskripsi' => 'Jus semangka segar tanpa alkohol.'],
            ['nama_menu' => 'Dragon Fruit Juice', 'harga' => 23000, 'kategori' => 'Fresh & Fruity', 'deskripsi' => 'Jus buah naga segar.'],
            ['nama_menu' => 'Orange Juice', 'harga' => 23000, 'kategori' => 'Fresh & Fruity', 'deskripsi' => 'Jus jeruk segar alami.'],
            ['nama_menu' => 'Pineapple Juice', 'harga' => 23000, 'kategori' => 'Fresh & Fruity', 'deskripsi' => 'Jus nanas segar.'],
            ['nama_menu' => 'Terong Belanda Juice', 'harga' => 23000, 'kategori' => 'Fresh & Fruity', 'deskripsi' => 'Jus terong belanda segar khas.'],
            ['nama_menu' => 'Avocado Juice', 'harga' => 25000, 'kategori' => 'Fresh & Fruity', 'deskripsi' => 'Jus alpukat creamy dan menyehatkan.'],
            ['nama_menu' => 'Mango Juice', 'harga' => 25000, 'kategori' => 'Fresh & Fruity', 'deskripsi' => 'Jus mangga manis dan segar.'],
            ['nama_menu' => 'Cucumber Juice', 'harga' => 23000, 'kategori' => 'Fresh & Fruity', 'deskripsi' => 'Jus timun segar dan menyehatkan.'],
            ['nama_menu' => 'Carrot Juice', 'harga' => 23000, 'kategori' => 'Fresh & Fruity', 'deskripsi' => 'Jus wortel segar dan bergizi.'],
            ['nama_menu' => 'Kuweni Juice', 'harga' => 23000, 'kategori' => 'Fresh & Fruity', 'deskripsi' => 'Jus kuweni dengan aroma khas.'],
            // Mocktails
            ['nama_menu' => 'Ocean Blue', 'harga' => 23000, 'kategori' => 'Mocktails', 'deskripsi' => 'Mocktail biru segar dengan soda dan sirup.'],
            ['nama_menu' => 'Classic Mojito', 'harga' => 23000, 'kategori' => 'Mocktails', 'deskripsi' => 'Mojito klasik dengan mint dan lime.'],
            ['nama_menu' => 'Mango Mojito', 'harga' => 23000, 'kategori' => 'Mocktails', 'deskripsi' => 'Mojito dengan mangga segar.'],
            ['nama_menu' => 'Sunset', 'harga' => 23000, 'kategori' => 'Mocktails', 'deskripsi' => 'Mocktail warna sunset dengan buah tropis.'],
            ['nama_menu' => 'Roy Rogers', 'harga' => 20000, 'kategori' => 'Mocktails', 'deskripsi' => 'Mocktail cola dengan grenadine.'],
            ['nama_menu' => 'Green Lagoon Twist', 'harga' => 23000, 'kategori' => 'Mocktails', 'deskripsi' => 'Mocktail hijau segar dengan soda dan lime.'],
            // Smoothies
            ['nama_menu' => 'Banana Yakult Smoothie', 'harga' => 30000, 'kategori' => 'Smoothies', 'deskripsi' => 'Smoothie pisang dengan yakult yang menyegarkan.'],
            ['nama_menu' => 'Strawberry Banana Smoothie', 'harga' => 35000, 'kategori' => 'Smoothies', 'deskripsi' => 'Smoothie stroberi dan pisang yang creamy.'],
            ['nama_menu' => 'Mango Smoothie', 'harga' => 30000, 'kategori' => 'Smoothies', 'deskripsi' => 'Smoothie mangga manis dan segar.'],
            // Tea Pot
            ['nama_menu' => 'Chamomile Tea Pot', 'harga' => 30000, 'kategori' => 'Tea Pot', 'deskripsi' => 'Teh chamomile premium dalam teko.'],
            ['nama_menu' => 'Jasmine Green Tea Pot', 'harga' => 30000, 'kategori' => 'Tea Pot', 'deskripsi' => 'Teh hijau melati premium dalam teko.'],
            ['nama_menu' => 'English Breakfast Tea Pot', 'harga' => 30000, 'kategori' => 'Tea Pot', 'deskripsi' => 'Teh English Breakfast dalam teko.'],
            ['nama_menu' => 'Earl Gray Tea Pot', 'harga' => 30000, 'kategori' => 'Tea Pot', 'deskripsi' => 'Teh Earl Gray premium dalam teko.'],
            ['nama_menu' => 'Wedang Kelor Nusantara', 'harga' => 30000, 'kategori' => 'Tea Pot', 'deskripsi' => 'Wedang kelor khas nusantara dalam teko.'],
            ['nama_menu' => 'Teh Rempah Jeruk Nipis', 'harga' => 30000, 'kategori' => 'Tea Pot', 'deskripsi' => 'Teh rempah dengan jeruk nipis dalam teko.'],
            // Others
            ['nama_menu' => 'Coca Cola', 'harga' => 15000, 'kategori' => 'Others', 'deskripsi' => 'Minuman bersoda Coca Cola.'],
            ['nama_menu' => 'Coke Zero', 'harga' => 15000, 'kategori' => 'Others', 'deskripsi' => 'Coca Cola tanpa gula.'],
            ['nama_menu' => 'Sprite', 'harga' => 15000, 'kategori' => 'Others', 'deskripsi' => 'Minuman bersoda Sprite.'],
            ['nama_menu' => 'Mineral Water', 'harga' => 10000, 'kategori' => 'Others', 'deskripsi' => 'Air mineral kemasan.'],
            ['nama_menu' => 'Ice Kosong', 'harga' => 4000, 'kategori' => 'Others', 'deskripsi' => 'Es batu kosong.'],
        ];

        foreach ($menus as $m) {
            Menu::create([
                'nama_menu'       => $m['nama_menu'],
                'harga'           => $m['harga'],
                'kategori_menu_id'=> $cats[$m['kategori']]->id,
                'status_menu_id'  => 1,
                'deskripsi'       => $m['deskripsi'],
                'stok'            => 100,
            ]);
        }

        $this->command->info('RestoranSeeder: ' . count($menus) . ' Menu & ' . count($categories) . ' Kategori Berhasil Dibuat!');
    }
}
