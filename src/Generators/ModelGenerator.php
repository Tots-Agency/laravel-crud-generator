<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use TOTS\LaravelCrudGenerator\FileGenerator;
use Illuminate\Support\Str;

class ModelGenerator extends FileGenerator
{
    protected string $table = '';
    protected string $primaryKey = '';
    protected string $fillable = '';
    protected string $accessors = '';
    protected string $mutators = '';
    protected string $relations = '';

    public function setFileContent() : void
    {
        $this->setTable();
        $this->setPrimaryKey();
        $this->setFillable();
        $this->setRelations();
    }

    public function setClassname() : void
    {
        $this->classname = $this->fileData && property_exists( $this->fileData, 'classname' )? $this->fileData->classname : $this->entityName;
    }

    public function setTable() : void
    {
        if( $this->fileData && property_exists( $this->fileData, 'table' ) )
            $this->table = "protected \$table = '" . $this->fileData->table . "';\n\t";
    }

    public function setPrimaryKey() : void
    {
        if( $this->fileData && property_exists( $this->fileData, 'primaryKey' ) )
            $this->primaryKey = "protected \$primaryKey = '" . $this->fileData->primaryKey . "';\n\t";
    }

    public function setFillable() : void
    {
        if( $this->entityData && property_exists( $this->entityData, 'attributes' ) && !empty( $this->entityData->attributes ) )
        {
            $attributes = $this->setAttributes( $this->entityData->attributes );
            $this->fillable = "protected \$fillable = [" . $attributes . "\n\t];\n";
        }
    }

    public function setAttributes( object $attributes ) : string
    {
        $stringAttributes = '';
        foreach( $attributes as $attributeName => $object )
            $stringAttributes .= "\n\t\t'" . $attributeName . "',";
        return $stringAttributes;
    }

    public function setRelations() : void
    {
        if( $this->entityData && property_exists( $this->entityData, 'relations' ) && !empty( $this->entityData->relations ) )
        {
            foreach( $this->entityData->relations as $relationType => $relations )
            {
                if( $relations != new \stdClass() )
                {
                    $relationType = Str::studly( $relationType );
                    $this->addFileUseUrl( "Illuminate\\Database\\Eloquent\\Relations\\" . $relationType );
                    $this->relations .= $this->setRelationMethods( $relationType, $relations );
                }
            }
        }
    }

    public function setRelationMethods( string $relationType, object $relations ) : string
    {
        $modelRelations = '';
        foreach( $relations as $modelRelation => $relationData )
        {
            if( $relationType != 'MorphTo' )
            {
                $classUrl = property_exists( $relationData, 'related' )? $relationData->related : $this->configurationOptions[ 'model' ][ 'namespace' ] . '\\' . $modelRelation;
                $this->addFileUseUrl( $classUrl );
            }

            $method = 'get' .  $relationType . 'RelationData';
            $templateData = $this->$method( $modelRelation, $relationData );
            $modelRelations .= parent::generateFromTemplate( 'relation', $templateData );
        }
        return $modelRelations;
    }

    public function getBelongsToRelationData( string $modelRelation, object $relationData ) : array
    {
        $foreingKey = $relationData->foreingKey ?? Str::snake( $modelRelation ) . '_id';
        $localKey = $relationData->localKey ?? $this->fileData->primaryKey ?? 'id';
        $relation = property_exists( $relationData, 'relation' )? ", '" . $relationData->relation . "'" : '';
        return [
            'relation_name' => $relationData->relationName ?? Str::camel( $modelRelation ),
            'relation' => 'BelongsTo',
            'relation_method' => 'belongsTo',
            'relation_content' => "$modelRelation::class, '$foreingKey', '$localKey'" . $relation,
        ];
    }

    public function getHasOneRelationData( string $modelRelation, object $relationData ) : array
    {
        $foreingKey = $relationData->foreingKey ?? Str::snake( $modelRelation );
        $localKey = $relationData->localKey ?? $this->fileData->primaryKey ?? 'id';
        return [
            'relation_name' => $relationData->relationName ?? Str::camel( $modelRelation ),
            'relation' => 'HasOne',
            'relation_method' => 'hasOne',
            'relation_content' => "$modelRelation::class, '$foreingKey', '$localKey'",
        ];
    }

    public function getBelongsToManyRelationData( string $modelRelation, object $relationData ) : array
    {
        $table = $relationData->table ?? Str::snake( Str::singular( $this->entityName ) ) . '_' . Str::snake( $modelRelation );
        $foreignPivotKey = $relationData->foreignPivotKey ?? Str::snake( Str::singular( $this->entityName ) ) . '_id';
        $relatedPivotKey = $relationData->relatedPivotKey ?? Str::snake( $modelRelation ) . '_id';
        $parentKey = $relationData->parentKey ?? $this->fileData->primaryKey ?? 'id';
        $relatedKey = $relationData->relatedKey ?? 'id';
        $relation = property_exists( $relationData, 'relation' )? ", '" . $relationData->relation . "'" : '';
        return [
            'relation_name' => $relationData->relationName ?? Str::plural( Str::camel( $modelRelation ) ),
            'relation' => 'BelongsToMany',
            'relation_method' => 'belongsToMany',
            'relation_content' => "$modelRelation::class, '$table', '$foreignPivotKey', '$relatedPivotKey', '$parentKey', '$relatedKey'" . $relation,
        ];
    }

    public function getHasManyRelationData( string $modelRelation, object $relationData ) : array
    {
        $foreingKey = $relationData->foreingKey ?? Str::snake( $modelRelation );
        $localKey = $relationData->localKey ?? $this->fileData->primaryKey ?? 'id';
        return [
            'relation_name' => $relationData->relationName ?? Str::plural( Str::camel( $modelRelation ) ),
            'relation' => 'HasMany',
            'relation_method' => 'hasMany',
            'relation_content' => "$modelRelation::class, '$foreingKey', '$localKey'",
        ];
    }
    public function getHasManyThroughRelationData( string $modelRelation, object $relationData ) : array
    {
        $through = $relationData->through ?? $this->entityName . $modelRelation;
        if( strpos( $through, '\\' ) !== false )
        {
            $this->addFileUseUrl( $through );
            $through = explode( '\\', $through );
            $through = end( $through );
        }
        $firstKey = $relationData->firstKey ?? Str::snake( $this->entityName ) . '_id';
        $secondKey = $relationData->secondKey ?? Str::snake( $modelRelation ) . '_id';
        $localKey = $relationData->localKey ?? 'id';
        $secondLocalKey = $relationData->secondLocalKey ?? 'id';
        return [
            'relation_name' => $relationData->relationName ?? Str::plural( Str::camel( $modelRelation ) ),
            'relation' => 'HasManyThrough',
            'relation_method' => 'hasManyThrough',
            'relation_content' => "$modelRelation::class, $through::class, '$firstKey', '$secondKey', '$localKey', '$secondLocalKey'",
        ];
    }

    public function getMorphToRelationData( string $modelRelation, object $relationData )
    {
        $relationContent = "";
        if( property_exists( $relationData, 'type' ) || property_exists( $relationData, 'id' ) || property_exists( $relationData, 'owner' ) )
        {
            $name = '__FUNCTION__';
            $type = $relationData->type ?? $modelRelation . '_type';
            $id = $relationData->id ?? $modelRelation . '_id';
            $owner = property_exists( $relationData, 'owner' )? ", '" . $relationData->owner . "'" : null;
            $relationContent = "$name, '$type', '$id'" . $owner;
        }

        return [
            'relation_name' => $modelRelation,
            'relation' => 'MorphTo',
            'relation_method' => 'morphTo',
            'relation_content' => $relationContent,
        ];
    }

    public function getMorphManyRelationData( string $modelRelation, object $relationData ) : array
    {
        $name = $relationData->name ?? Str::camel( $modelRelation );
        $relationContent = "$modelRelation::class, '$name'";
        if( property_exists( $relationData, 'type' ) || property_exists( $relationData, 'id' ) || property_exists( $relationData, 'localKey' ) )
        {
            $type = $relationData->type ?? $modelRelation . '_type';
            $id = $relationData->id ?? $modelRelation . '_id';
            $localKey = property_exists( $relationData, 'localKey' )? ", '" . $relationData->localKey . "'" : null;
            $relationContent .= ", '$type', '$id'" . $localKey;
        }
        return [
            'relation_name' => $relationData->relationName ?? Str::camel( Str::plural( $modelRelation ) ),
            'relation' => 'MorphMany',
            'relation_method' => 'morphMany',
            'relation_content' => $relationContent,
        ];
    }

    public function getMorphOneRelationData( string $modelRelation, object $relationData ) : array
    {
        $name = $relationData->name ?? Str::sanke( $modelRelation );
        $relationContent = "$modelRelation::class, '$name'";
        if( property_exists( $relationData, 'type' ) || property_exists( $relationData, 'id' ) || property_exists( $relationData, 'localKey' ) )
        {
            $type = $relationData->type ?? $modelRelation . '_type';
            $id = $relationData->id ?? $modelRelation . '_id';
            $localKey = property_exists( $relationData, 'localKey' )? ", '" . $relationData->localKey . "'" : null;
            $relationContent .= ", '$type', '$id'" . $localKey;
        }
        return [
            'relation_name' => $relationData->relationName ?? Str::camel( $modelRelation ),
            'relation' => 'MorphOne',
            'relation_method' => 'morphOne',
            'relation_content' => $relationContent,
        ];
    }

    public function generateFileContent() : void
    {
        parent::generateFileContent();
        $this->fileContent = str_replace( '{{ table }}', $this->table, $this->fileContent );
        $this->fileContent = str_replace( '{{ primary_key }}', $this->primaryKey, $this->fileContent );
        $this->fileContent = str_replace( '{{ fillable }}', $this->fillable, $this->fileContent );
        $this->fileContent = str_replace( '{{ accessors }}', $this->accessors, $this->fileContent );
        $this->fileContent = str_replace( '{{ mutators }}', $this->mutators, $this->fileContent );
        $this->fileContent = str_replace( '{{ relations }}', $this->relations, $this->fileContent );
        $this->fileContent = str_replace( '(  )', '()', $this->fileContent );
    }
}
