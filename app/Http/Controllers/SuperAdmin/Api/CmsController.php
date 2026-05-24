<?php

namespace App\Http\Controllers\SuperAdmin\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    // ── Settings ──────────────────────────────────────────────────────────────

    public function getSettings(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data"   => [
                "company_name"  => CmsSetting::get("company_name", "NexaERP"),
                "tagline_en"    => CmsSetting::get("tagline_en", "The Future of Business Management"),
                "tagline_ar"    => CmsSetting::get("tagline_ar", "مستقبل إدارة الأعمال"),
                "email"         => CmsSetting::get("email", "hello@nexaerp.com"),
                "phone"         => CmsSetting::get("phone", "+1 (234) 567-890"),
                "address_en"    => CmsSetting::get("address_en", "123 Tech Park, Silicon Valley"),
                "address_ar"    => CmsSetting::get("address_ar", "123 تك بارك، وادي السيليكون"),
            ],
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $data = $request->validate([
            "company_name" => "sometimes|string|max:100",
            "tagline_en"   => "sometimes|string|max:200",
            "tagline_ar"   => "sometimes|string|max:200",
            "email"        => "sometimes|email",
            "phone"        => "sometimes|string|max:30",
            "address_en"   => "sometimes|string|max:255",
            "address_ar"   => "sometimes|string|max:255",
        ]);

        foreach ($data as $key => $value) {
            CmsSetting::set($key, $value);
        }

        return response()->json(["status" => "success", "message" => "Settings updated."]);
    }

    // ── Hero ──────────────────────────────────────────────────────────────────

    public function getHero(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data"   => CmsSetting::get("hero", [
                "badge_en"          => "Trusted by 500+ companies worldwide",
                "badge_ar"          => "موثوق من قبل أكثر من 500 شركة",
                "title_en"          => "Scale Your Business with NexaERP",
                "title_ar"          => "طوّر أعمالك مع NexaERP",
                "subtitle_en"       => "The all-in-one cloud ERP platform.",
                "subtitle_ar"       => "منصة ERP السحابية المتكاملة.",
                "cta_primary_en"    => "Get Started Free",
                "cta_primary_ar"    => "ابدأ مجاناً",
                "cta_secondary_en"  => "Watch Demo",
                "cta_secondary_ar"  => "شاهد العرض",
            ]),
        ]);
    }

    public function updateHero(Request $request): JsonResponse
    {
        CmsSetting::set("hero", $request->all());
        return response()->json(["status" => "success", "message" => "Hero updated."]);
    }

    // ── Features ──────────────────────────────────────────────────────────────

    public function getFeatures(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data"   => CmsSetting::get("features", [
                ["icon" => "fa-users",          "color" => "blue",   "title_en" => "HR Management",    "title_ar" => "إدارة الموارد البشرية", "desc_en" => "Streamline hiring, attendance, and performance.", "desc_ar" => "أتمتة التوظيف والحضور والأداء."],
                ["icon" => "fa-boxes-stacked",  "color" => "cyan",   "title_en" => "Inventory Control","title_ar" => "إدارة المخزون",          "desc_en" => "Real-time stock tracking.",                       "desc_ar" => "تتبع المخزون لحظياً."],
                ["icon" => "fa-cash-register",  "color" => "purple", "title_en" => "POS System",       "title_ar" => "نقاط البيع",             "desc_en" => "Lightning-fast point of sale.",                  "desc_ar" => "نقاط بيع سريعة."],
                ["icon" => "fa-handshake",      "color" => "green",  "title_en" => "CRM",              "title_ar" => "إدارة العملاء",          "desc_en" => "Manage leads and deals.",                        "desc_ar" => "إدارة العملاء المحتملين."],
                ["icon" => "fa-money-bill-wave","color" => "yellow", "title_en" => "Payroll",          "title_ar" => "الرواتب",                "desc_en" => "Automated salary calculations.",                 "desc_ar" => "حساب الرواتب تلقائياً."],
                ["icon" => "fa-chart-line",     "color" => "red",    "title_en" => "Accounting",       "title_ar" => "المحاسبة",               "desc_en" => "Full double-entry bookkeeping.",                 "desc_ar" => "محاسبة مزدوجة كاملة."],
            ]),
        ]);
    }

    public function updateFeatures(Request $request): JsonResponse
    {
        CmsSetting::set("features", $request->input("features", []));
        return response()->json(["status" => "success", "message" => "Features updated."]);
    }

    // ── Testimonials ──────────────────────────────────────────────────────────

    public function getTestimonials(): JsonResponse
    {
        return response()->json([
            "status" => "success",
            "data"   => CmsSetting::get("testimonials", [
                ["name" => "Sarah Johnson", "role" => "CEO, TechCorp",          "quote_en" => "NexaERP transformed how we manage our team.", "quote_ar" => "غيّر NexaERP طريقة إدارتنا.", "rating" => 5, "initials" => "SJ"],
                ["name" => "Michael Chen",  "role" => "Operations Director",    "quote_en" => "Real-time tracking across 5 warehouses.",     "quote_ar" => "تتبع فوري عبر 5 مستودعات.",  "rating" => 5, "initials" => "MC"],
                ["name" => "Aisha Al-Rashid","role" => "CFO, RetailMax",        "quote_en" => "The accounting module is incredible.",        "quote_ar" => "وحدة المحاسبة رائعة.",        "rating" => 5, "initials" => "AA"],
            ]),
        ]);
    }

    public function updateTestimonials(Request $request): JsonResponse
    {
        CmsSetting::set("testimonials", $request->input("testimonials", []));
        return response()->json(["status" => "success", "message" => "Testimonials updated."]);
    }
}