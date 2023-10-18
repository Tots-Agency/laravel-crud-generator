<?php

namespace App\Models;

use App\Models\Images\Image;
use App\Models\Posts\Post;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductTag;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Product extends Authenticatable implements MustVerifyEmail
{
	use HasApiTokens, Notifiable;

	protected $table = 'products';
	protected $primaryKey = 'idproduct';
	protected $fillable = [
		'name',
		'description',
		'category_id',
	];

	public function brand() : BelongsTo
	{
		return $this->belongsTo( Brand::class, 'brand_id', 'idproduct' );
	}

	public function productCategory() : BelongsTo
	{
		return $this->belongsTo( ProductCategory::class, 'category_id', 'idcategory', 'product_category_relation_name' );
	}

	public function post() : HasOne
	{
		return $this->hasOne( Post::class, 'product_id', 'id' );
	}

	public function productTags() : BelongsToMany
	{
		return $this->belongsToMany( ProductTag::class, 'tags_products', 'productid', 'tagid', 'idproducto', 'idtag', 'product_tag_relation_name' );
	}

	public function saleItems() : HasMany
	{
		return $this->hasMany( SaleItem::class, 'product_id', 'id' );
	}

	public function sales() : HasManyThrough
	{
		return $this->hasManyThrough( Sale::class, SaleItem::class, 'product_id', 'idsale', 'item_id', 'sale_id' );
	}

	public function imageable() : MorphTo
	{
		return $this->morphTo( __FUNCTION__, 'imageable_type', 'imageable_id', 'id' );
	}

	public function testeable() : MorphTo
	{
		return $this->morphTo();
	}

	public function images() : MorphMany
	{
		return $this->morphMany( Image::class, 'imageable' );
	}

	public function image() : MorphOne
	{
		return $this->morphOne( Image::class, 'imageable' );
	}

}
