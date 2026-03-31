<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Review;
use App\Models\ServiceVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GlassSkinFacialReviewSeeder extends Seeder
{
    public function run(): void
    {
        $variant = ServiceVariant::with('service')->where('slug', 'glass-skin-facial')->first();

        if (!$variant || !$variant->service) {
            $this->command?->error('Glass Skin Facial variant not found.');
            return;
        }

        $rows = [
            ['name' => 'Aarohi Mehta', 'rating' => 5, 'comment' => 'Skin looked visibly brighter right after the session. The glow lasted for days.'],
            ['name' => 'Priya Sharma', 'rating' => 5, 'comment' => 'Very relaxing and worth the price. My face felt fresh and hydrated.'],
            ['name' => 'Neha Kapoor', 'rating' => 4, 'comment' => 'Good facial and neat finish. I only wish the massage part was a little longer.'],
            ['name' => 'Simran Arora', 'rating' => 5, 'comment' => 'Loved the result. Makeup sat much better on my skin after this facial.'],
            ['name' => 'Ishita Jain', 'rating' => 4, 'comment' => 'Nice service and clean process. My skin felt smoother instantly.'],
            ['name' => 'Riya Bansal', 'rating' => 5, 'comment' => 'One of the best facials I have tried at home service level.'],
            ['name' => 'Tanvi Sethi', 'rating' => 5, 'comment' => 'Visible glow and no irritation at all. Perfect for an event prep.'],
            ['name' => 'Kashish Malhotra', 'rating' => 4, 'comment' => 'The finish was very nice and brightening. Good experience overall.'],
            ['name' => 'Ananya Verma', 'rating' => 5, 'comment' => 'Face looked plump and fresh. I got compliments the same evening.'],
            ['name' => 'Mehak Gupta', 'rating' => 5, 'comment' => 'Hydration and glow were excellent. I would book this variant again.'],
            ['name' => 'Suhani Khanna', 'rating' => 4, 'comment' => 'Service quality was strong and products felt premium.'],
            ['name' => 'Niharika Rao', 'rating' => 5, 'comment' => 'Great for dull skin. It gave a healthy shine without feeling greasy.'],
            ['name' => 'Avantika Singh', 'rating' => 5, 'comment' => 'Professional handling and really nice result on sensitive skin.'],
            ['name' => 'Bhavya Nair', 'rating' => 4, 'comment' => 'Good glow and soft skin. The cleanup before the facial was done well.'],
            ['name' => 'Sana Ali', 'rating' => 5, 'comment' => 'My skin texture looked smoother and pores appeared reduced.'],
            ['name' => 'Muskan Chawla', 'rating' => 5, 'comment' => 'Perfect facial before a function. Skin looked clear and radiant.'],
            ['name' => 'Pallavi Desai', 'rating' => 4, 'comment' => 'Really nice finish. Slightly expensive, but the result was good.'],
            ['name' => 'Khushi Anand', 'rating' => 5, 'comment' => 'It gave a polished and glass-like shine exactly as expected.'],
            ['name' => 'Nupur Goyal', 'rating' => 5, 'comment' => 'Relaxing session and clear visible difference in brightness.'],
            ['name' => 'Vidhi Joshi', 'rating' => 4, 'comment' => 'Good hydration and softness. Skin felt very fresh afterward.'],
            ['name' => 'Pooja Iyer', 'rating' => 5, 'comment' => 'The best part was the smoothness. Face looked clean and healthy.'],
            ['name' => 'Shreya Kulkarni', 'rating' => 5, 'comment' => 'I liked how even-toned my face looked after the treatment.'],
            ['name' => 'Aditi Oberoi', 'rating' => 4, 'comment' => 'Nice variant for regular glow maintenance. Would recommend it.'],
            ['name' => 'Tanya Bhatia', 'rating' => 5, 'comment' => 'Very impressive results. Skin stayed soft and glowing for days.'],
            ['name' => 'Radhika Sen', 'rating' => 5, 'comment' => 'Comfortable experience and the glow was natural, not overdone.'],
            ['name' => 'Komal Yadav', 'rating' => 4, 'comment' => 'A solid facial option. I noticed smoother skin and better texture.'],
            ['name' => 'Mitali Roy', 'rating' => 5, 'comment' => 'The finish was beautiful. It worked especially well before makeup.'],
            ['name' => 'Parul Arvind', 'rating' => 5, 'comment' => 'Great product feel and visible hydration boost after one session.'],
            ['name' => 'Jhanvi Bhalla', 'rating' => 4, 'comment' => 'Nice professional service and the skin glow was definitely visible.'],
            ['name' => 'Diya Luthra', 'rating' => 5, 'comment' => 'Excellent result. This variant deserves to be one of the top picks.'],
        ];

        DB::transaction(function () use ($rows, $variant) {
            $notes = collect(range(1, count($rows)))
                ->map(fn ($index) => 'seed:glass-skin-facial-review-' . $index)
                ->all();

            Review::query()
                ->whereHas('booking', fn ($query) => $query->whereIn('notes', $notes))
                ->delete();

            Booking::query()->whereIn('notes', $notes)->delete();

            foreach ($rows as $index => $row) {
                $number = $index + 1;
                $mobile = '900000' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
                $customer = Customer::firstOrCreate(
                    ['mobile' => $mobile],
                    [
                        'name' => $row['name'],
                        'email' => 'glass.skin.' . $number . '@example.com',
                        'status' => 'Active',
                        'bookings' => 0,
                        'joined' => Carbon::now()->subDays(120 + $number)->toDateString(),
                    ]
                );

                $customer->forceFill([
                    'name' => $row['name'],
                    'email' => 'glass.skin.' . $number . '@example.com',
                    'status' => 'Active',
                ])->save();

                $bookingDate = Carbon::now()->subDays(40 - $number);

                $booking = Booking::create([
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_avatar' => $customer->avatar,
                    'customer_phone' => $customer->mobile,
                    'service_id' => $variant->service_id,
                    'service_name' => $variant->service->name,
                    'service_variant_id' => $variant->id,
                    'professional_id' => null,
                    'professional_name' => 'Seeded Demo Professional',
                    'date' => $bookingDate->toDateString(),
                    'slot' => '11:00 AM - 12:00 PM',
                    'status' => 'Completed',
                    'price' => $variant->display_price,
                    'notes' => 'seed:glass-skin-facial-review-' . $number,
                    'created_at' => $bookingDate->copy()->subHours(2),
                    'updated_at' => $bookingDate->copy()->subHours(1),
                ]);

                Review::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $customer->id,
                    'service_id' => $variant->service_id,
                    'service_variant_id' => $variant->id,
                    'rating' => $row['rating'],
                    'comment' => $row['comment'],
                    'status' => 'Approved',
                    'customer_name' => $customer->name,
                    'customer_avatar' => $customer->avatar,
                    'review_type' => 'text',
                    'created_at' => $bookingDate->copy()->addHours(4),
                    'updated_at' => $bookingDate->copy()->addHours(4),
                ]);
            }
        });

        $this->command?->info('Seeded 30 Glass Skin Facial reviews.');
    }
}
