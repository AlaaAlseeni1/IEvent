<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // نمرّ على كل الإعدادات لأن الملفات المرفوعة لا تظهر ضمن $request->settings
        foreach (Setting::all() as $setting) {
            $key = $setting->key;

            if ($setting->type == 'image') {
                // حذف الصورة عند طلب ذلك
                if ($request->boolean("remove_settings.$key")) {
                    $this->deleteLegacyFile($setting->value);
                    $setting->update(['value' => null]);
                    continue;
                }

                if ($request->hasFile("settings.$key")) {
                    $request->validate([
                        "settings.$key" => 'image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048',
                    ], [], ["settings.$key" => $this->labelFor($key)]);

                    // حذف ملف قديم إن وُجد (توافق مع الطريقة السابقة)
                    $this->deleteLegacyFile($setting->value);

                    // نخزّن الصورة كـ base64 داخل قاعدة البيانات لتبقى بعد كل نشر
                    $file = $request->file("settings.$key");
                    $mime = $file->getMimeType();
                    $data = base64_encode(file_get_contents($file->getRealPath()));
                    $setting->update(['value' => "data:{$mime};base64,{$data}"]);
                }
                continue;
            }

            // حقول النص/اللون: حدّث فقط إن أُرسلت
            if ($request->has("settings.$key")) {
                $setting->update(['value' => $request->input("settings.$key")]);
            }
        }

        return redirect()->route('settings.index')->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    // حذف ملف مخزّن بالطريقة القديمة (مسار داخل storage) دون المساس بقيم base64
    private function deleteLegacyFile(?string $value): void
    {
        if ($value && !str_starts_with($value, 'data:')) {
            Storage::disk('public')->delete($value);
        }
    }

    private function labelFor(string $key): string
    {
        return [
            'platform_logo'   => 'شعار المنصة',
            'platform_favicon'=> 'أيقونة المنصة',
        ][$key] ?? $key;
    }
}
