<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {

            // INVOICE
            $table->string('invoice_no')
                ->nullable()
                ->after('id');

            // DISKON
            $table->decimal('discount', 15, 2)
                ->default(0)
                ->after('sale_date');

        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {

            $table->dropColumn('invoice_no');

            $table->dropColumn('discount');

        });
    }
};
