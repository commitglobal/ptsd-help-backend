<?php

use App\Imports\CountriesImport;
use App\Imports\LanguagesImport;
use App\Models\Country;
use App\Models\Language;
use App\Models\Version;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'name');
            $table->string('status')->default('drafted');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->string('id', 2)->primary();
            $table->string(column: 'name');

            $table->timestamps();
        });

        Schema::create('languages', function (Blueprint $table) {
            $table->string('id', 2)->primary();
            $table->string(column: 'name');

            $table->timestamps();
        });

        Excel::import(new CountriesImport, 'countries.csv', 'seed-data');
        Excel::import(new LanguagesImport, 'languages.csv', 'seed-data');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('versions');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('languages');
    }
};
