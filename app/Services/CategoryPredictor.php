<?php
namespace App\Services;

use Gemini;
use App\Models\Tenant\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class CategoryPredictor
{
    public function predict(string $title)
    {

        Log::info('DB CHECK', [
    'database' => DB::connection()->getDatabaseName(),
    'category_count' => Category::count(),
]);
        $client = Gemini::client(config('services.gemini.key'));

        $prompt = <<<PROMPT
You are a Shopify taxonomy classifier.

Product title:
"$title"

Respond ONLY with valid JSON:
{"category_name":"...","seo_title":"..."}
PROMPT;

        try {
      $result = $client->geminiFlash()->generateContent($prompt);


            Log::info('GEMINI RAW', [
    'text' => $result->text()
]);
            $text = trim($result->text());

            preg_match('/\{.*\}/s', $text, $matches);

            if (!isset($matches[0])) {
                Log::error('Invalid Gemini response', ['text' => $text]);
                return null;
            }

            $response = json_decode($matches[0], true);

            if (!isset($response['category_name'])) {
                return null;
            }

            return $this->smartMatch($response['category_name'], $title);

        } catch (\Exception $e) {
            Log::error("Gemini Error: " . $e->getMessage());
            return null;
        }
    }

    private function smartMatch(string $aiCategory, string $title)
    {
        $keywords = array_unique(
            array_filter(
                explode(' ', strtolower($aiCategory . ' ' . $title)),
                fn($w) => strlen($w) > 3
            )
        );

        $query = Category::query();

        foreach ($keywords as $word) {
            $query->where('name', 'LIKE', "%$word%");
        }

        return $query->orderBy('level', 'desc')->first();
    }
}

