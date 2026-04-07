<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Laravel creates enum columns as varchar with a CHECK constraint on PostgreSQL.
        // Drop the old check constraint and add a new one with the additional values.
        $allValues = [
            'property_photo',
            'business_permit',
            'bir_2303',
            'inspection_report',
            'barangay_clearance',
            'occupancy_permit',
            'title_tct',
            'tax_declaration',
            'transfer_certificate',
        ];

        $valuesList = implode(', ', array_map(fn($v) => "'{$v}'::character varying", $allValues));

        DB::statement("ALTER TABLE property_documents DROP CONSTRAINT IF EXISTS property_documents_category_check");
        DB::statement("ALTER TABLE property_documents ADD CONSTRAINT property_documents_category_check CHECK (category::text = ANY (ARRAY[{$valuesList}]::text[]))");
    }

    public function down(): void
    {
        DB::table('property_documents')
            ->whereIn('category', ['title_tct', 'tax_declaration', 'transfer_certificate'])
            ->delete();

        $originalValues = [
            'property_photo',
            'business_permit',
            'bir_2303',
            'inspection_report',
            'barangay_clearance',
            'occupancy_permit',
        ];

        $valuesList = implode(', ', array_map(fn($v) => "'{$v}'::character varying", $originalValues));

        DB::statement("ALTER TABLE property_documents DROP CONSTRAINT IF EXISTS property_documents_category_check");
        DB::statement("ALTER TABLE property_documents ADD CONSTRAINT property_documents_category_check CHECK (category::text = ANY (ARRAY[{$valuesList}]::text[]))");
    }
};
