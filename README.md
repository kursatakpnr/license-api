# Dijital Lisans API

Laravel tabanlı bir REST API servisi. Kullanıcıların dijital yazılım lisansı satın alabilmesini, ürünleri güncel stok bilgileriyle listeleyebilmesini ve mevcut lisanslarını görüntüleyebilmesini sağlar.

Kod okunabilirliği, thin controller yaklaşımı ve Laravel standartlarına (Resource, Form Request) uyum gözetilerek geliştirilmiştir.

---

## Kullanılan Teknolojiler

- **PHP:** 8.3+
- **Framework:** Laravel 12
- **Veritabanı:** MySQL
- **ORM:** Eloquent
- **Asenkron İşlemler:** Laravel Queue (Database driver)

---

## Proje ve Veritabanı Mantığı

Sistem temel olarak 3 tablo üzerinden çalışır: `users`, `products` ve `licenses`.

Bir ürünün ve bir kullanıcının birden fazla lisansı olabilir (One-to-Many). `licenses` tablosundaki `user_id` alanı `NULL` ise, o lisans henüz satılmamış ve stokta bekliyor demektir. Satın alma işlemi sırasında ilgili ürüne ait `user_id`'si `NULL` olan ilk kayıt bulunur ve kullanıcıya atanır. Race condition riskini ortadan kaldırmak için bu işlem **Database Transaction** içinde gerçekleştirilir.

---

## Kurulum

**1. Projeyi klonlayın ve dizine girin:**
```
git clone <repo-url>
cd license-api
```

**2. Bağımlılıkları yükleyin:**
```
composer install
```

**3. Çevre değişkenlerini ayarlayın:**
```
cp .env.example .env
php artisan key:generate
```

`.env` dosyasını açıp MySQL bağlantı bilgilerinizi girin ve queue driver'ını aşağıdaki gibi ayarlayın:

```
DB_DATABASE=license_api
QUEUE_CONNECTION=database
```

**4. Veritabanını oluşturun ve örnek verileri ekleyin:**
```
php artisan migrate:fresh --seed
```

Bu komut tabloları oluşturur; Factory'ler aracılığıyla satılmış ve satılmamış lisansları da kapsayan örnek kullanıcı, ürün ve lisans kayıtlarını ekler.

**5. Geliştirme sunucusunu başlatın:**
```
php artisan serve
```

API varsayılan olarak `http://127.0.0.1:8000` adresinde çalışır.

---

## API Uçları (Endpoints)

Tüm yanıtlar, modeller doğrudan açığa çıkarılmadan **Eloquent Resource** sınıfları üzerinden filtrelenerek döner.

### Kullanıcıları Listele
`GET /api/users`

Sistemdeki tüm kullanıcıları döndürür.

---

### Ürünleri Listele
`GET /api/products`

Ürünleri listeler. Yanıtta her ürüne ait satılmamış lisans sayısını gösteren `available_stock` alanı yer alır.

---

### Kullanıcının Lisanslarını Listele
`GET /api/users/{user}/licenses`

Belirtilen kullanıcıya ait lisansları döndürür. Route Model Binding kullanılmıştır.

---

### Sipariş Oluştur
`POST /api/orders`

Kullanıcının bir ürün için lisans satın almasını sağlar.

**Örnek istek gövdesi:**
```
{
  "user_id": 1,
  "product_id": 1
}
```

İstek, controller'a ulaşmadan önce özel bir **Form Request** sınıfıyla doğrulanır. `user_id` veya `product_id` eksik/geçersizse ya da ürünün müsait lisansı kalmamışsa `422 Unprocessable Entity` döner.

---

## Asenkron Bildirim (Queue & Job)

Sipariş tamamlandığında ana akışı bloke etmemek için lisans teslimat süreci kuyruğa alınır. `SendLicenseEmailJob` tetiklenir ve işlem loglanır. Kuyruğu çalıştırmak için ayrı bir terminalde şu komutu kullanabilirsiniz:

```
php artisan queue:work --once
```

İşlem tamamlandığında `storage/logs/laravel.log` dosyasında ilgili log satırını görebilirsiniz.

---

## Raporlama (Artisan Console)

Stoktaki boş lisans sayısı 5'in altına düşen ürünleri listelemek için:

```
php artisan report:stock
```

Eşleşen ürünler terminal ekranında tablo formatında görüntülenir.

---

