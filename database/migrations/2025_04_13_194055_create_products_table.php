<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
        
            // Foreign keys with cascading behavior
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_category_id')->nullable()->constrained()->onDelete('set null');
            
            // For brand_id, make sure it's nullable and use foreign key constraint
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
        
            // Other columns
            $table->foreignId('merchant_id')->nullable();   // If the product is added by a merchant
        
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();               // Stock Keeping Unit (important for inventory systems)
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
        
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('affiliate_price', 10, 2)->nullable();
        
            $table->integer('stock')->default(0);               // Available stock
        
            $table->string('image')->nullable();                // Cover image
            $table->json('gallery_images')->nullable();         // For multiple images
        
            $table->integer('status')->default(1);              // ['active'=>1, 'inactive'=> 0, 'draft' => 2]
            $table->boolean('featured')->default(false);        // For homepage / special products
        
            $table->decimal('weight', 8, 2)->nullable();        // For shipping
            $table->string('unit')->nullable();                 // E.g. kg, gm, piece
        
            $table->json('meta')->nullable();                   // SEO metadata
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
