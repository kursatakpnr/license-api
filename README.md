# Dijital Lisans API

Laravel tabanlı bir REST API servisi. Kullanıcıların dijital yazılım lisansı satın alabilmesini, ürünleri güncel stok bilgileriyle listeleyebilmesini ve mevcut lisanslarını görüntüleyebilmesini sağlar.

Kod okunabilirliği, thin controller yaklaşımı ve Laravel standartlarına (Resource, Form Request) uyum gözetilerek geliştirilmiştir.

---

## Kullanılan Teknolojiler

- **PHP:** 8.3+
- **Framework:** Laravel 12
- **Veritabanı:** MySQL
- **ORM:** Eloquent
- **Bildirim:** Laravel Job yapısı (sync driver)

---

## Proje ve Veritabanı Mantığı

Sistem temel olarak 3 tablo üzerinden çalışır: `users`, `products` ve `licenses`.

Bir ürünün ve bir kullanıcının birden fazla lisansı olabilir (One-to-Many). `licenses` tablosundaki `user_id` alanı `NULL` ise, o lisans henüz satılmamış ve stokta bekliyor demektir. Satın alma işlemi sırasında ilgili ürüne ait `user_id`'si `NULL` olan ilk kayıt bulunur ve kullanıcıya atanır. Race condition riskini ortadan kaldırmak için bu işlem **Database Transaction** içinde gerçekleştirilir.

---

## Kurulum

**1. Projeyi klonlayın ve dizine girin:**
```bash
git clone <repo-url>
cd license-api
```

**2. Bağımlılıkları yükleyin:**
```bash
composer install
```

**3. Çevre değişkenlerini ayarlayın:**

Windows:
```bash
copy .env.example .env
```

Linux / macOS:
```bash
cp .env.example .env
```

```bash
php artisan key:generate
```

`.env` dosyasını açıp MySQL bağlantı bilgilerinizi girin:

```env
DB_DATABASE=license_api
QUEUE_CONNECTION=sync
```

**4. Veritabanını oluşturun ve örnek verileri ekleyin:**
```bash
php artisan migrate:fresh --seed
```

Bu komut tabloları oluşturur; Factory'ler aracılığıyla satılmış ve satılmamış lisansları da kapsayan örnek kullanıcı, ürün ve lisans kayıtlarını ekler.

**5. Geliştirme sunucusunu başlatın:**
```bash
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
```json
{
  "user_id": 1,
  "product_id": 1
}
```

İstek, controller'a ulaşmadan önce özel bir **Form Request** sınıfıyla doğrulanır. `user_id` veya `product_id` eksik/geçersizse ya da ürünün müsait lisansı kalmamışsa `422 Unprocessable Entity` döner.

---

## Job Tabanlı Bildirim

Satın alma işlemi tamamlandığında `SendLicenseEmailJob` tetiklenir ve işlem `Log::info()` aracılığıyla loglanır. Lokal ortamda ek tablo ihtiyacını önlemek adına `QUEUE_CONNECTION=sync` kullanılmıştır; job, istek akışı içinde eş zamanlı olarak çalışır.

İşlem tamamlandığında `storage/logs/laravel.log` dosyasında ilgili log satırını görebilirsiniz.

---

## Raporlama (Artisan Console)

Stoktaki boş lisans sayısı 5'in altına düşen ürünleri listelemek için:

```bash
php artisan report:stock
```

Eşleşen ürünler terminal ekranında tablo formatında görüntülenir.

---

