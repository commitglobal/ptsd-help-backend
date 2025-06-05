<?php

declare(strict_types=1);

use App\Imports\CountriesImport;
use App\Imports\LanguagesImport;
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

            $table->unique('name');

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
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

        Schema::create('version_country', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained('versions')->onDelete('cascade');

            $table->string('country_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');

            $table->json('tools');

            $table->unique(['version_id', 'country_id']);

            $table->timestamps();
        });

        Schema::create('version_country_language', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_country_id')->constrained('version_country')->onDelete('cascade');

            $table->string('language_id');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');

            $table->json('tools')->nullable();
            $table->json('symptoms')->nullable();
            $table->json('support')->nullable();

            $table->unique(['version_country_id', 'language_id']);

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
        Schema::dropIfExists('country_version');
        Schema::dropIfExists('country_version_language');
    }
};
