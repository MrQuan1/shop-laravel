<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("
            ALTER TABLE orders 
            MODIFY payment_method ENUM('cod','vnpay') 
            NOT NULL DEFAULT 'cod'
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE orders 
            MODIFY payment_method ENUM('cod') 
            NOT NULL DEFAULT 'cod'
        ");
    }
};
