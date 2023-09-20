<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use TOTS\LaravelCrudGenerator\FileGenerator;
use Illuminate\Support\Str;

class MigrationGenerator extends FileGenerator
{
    protected string $table = '';
    protected string $columns = '';
    protected string $relations = '';

    public function setFileContent() : void
    {
        $this->setTable();
        $this->setColumns();
        $this->setRelations();
        $this->setRelations();
    }

    public function setClassname() : void
    {
        $this->classname = $this->fileData && property_exists( $this->fileData, 'classname' )? $this->fileData->classname : 'Create' . Str::plural( $this->entityName ) . 'Table';
    }

    public function setTable() : void
    {
        $this->table = $this->fileData && property_exists( $this->fileData, 'table' )? $this->fileData->table : Str::snake( Str::plural( $this->entityName ) );
    }

    public function setColumns() : void
    {
        $this->columns = "\n";
        $this->columns .= $this->setIdColumn();
        if( $this->entityData && property_exists( $this->entityData, 'attributes' ) && !empty( $this->entityData->attributes ) )
        {
            foreach( $this->entityData->attributes as $attributeName => $object )
            {
                if( in_array( $object->type, [ 'id', 'timestamps', 'softDeletes' ] ) )
                {
                    $methodName = 'set' . Str::camel( $attributeName ) . 'Column';
                    $this->columns .= $this->$methodName( $object );
                }else{
                    $templateData = $this->createColumnTemplateData( $attributeName, $object );
                    $this->columns .= parent::generateFromTemplate( 'column', $templateData );
                }
            }
        }
        $this->columns .= $this->setTimestampsColumn();
        $this->columns .= $this->setSoftDeletesColumn();
    }

    public function setIdColumn() : string
    {
        $name = $this->fileData && property_exists( $this->fileData, 'id' ) && $this->fileData->id !== false? $this->fileData->id : null;
        $name =  $name ?? $this->configurationOptions[ 'migration' ][ 'id' ];
        if( !$name || $name === false ) return '';
        $name = $name === 'id' || $name === true? '' : " '$name' ";
        return "\t\t\t\$table->id($name);\n";
    }

    public function setTimestampsColumn() : string
    {
        $timestamps = $this->fileData && property_exists( $this->fileData, 'timestamps' ) && $this->fileData->timestamps !== false? $this->fileData->timestamps : null;
        $timestamps = $timestamps ?? $this->configurationOptions[ 'migration' ][ 'timestamps' ];
        if( !$timestamps || $timestamps === false ) return '';
        return "\t\t\t\$table->timestamps();\n";
    }

    public function setSoftDeletesColumn() : string
    {
        $softDeletes = $this->fileData && property_exists( $this->fileData, 'softDeletes' ) && $this->fileData->softDeletes !== false? $this->fileData->softDeletes : null;
        $softDeletes = $softDeletes ?? $this->configurationOptions[ 'migration' ][ 'softDeletes' ];
        if( !$softDeletes || $softDeletes === false ) return '';
        return "\t\t\t\$table->softDeletes();\n";
    }

    public function createColumnTemplateData( string $attributeName, object $object ) : array
    {
        $columnType = $object->type;
        $columnValue = $attributeName;
        $unique = property_exists( $object, 'unique' ) && $object->unique == true? '->unique()' : '';
        $nullable = property_exists( $object, 'nullable' ) && $object->nullable == true? '->nullable()' : '';
        $default = property_exists( $object, 'default' ) && $object->default? "->default( '$object->default' )" : '';
        $parameter1 = property_exists( $object, 'parameter1' ) && $object->parameter1 ?? '';
        $parameter2 = property_exists( $object, 'parameter2' ) && $object->parameter2 ?? '';

        return [
            'column_type' => $columnType,
            'column_value' => $columnValue,
            'parameter1' => $parameter1,
            'parameter2' => $parameter2,
            'unique' => $unique,
            'nullable' => $nullable,
            'default' => $default,
        ];
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
                    $this->relations .= $this->setRelationColumns( $relationType, $relations );
                }
            }
        }
    }

    public function setRelationColumns( string $relationType, object $relations ) : string
    {
        $modelRelations = '';
        foreach( $relations as $modelRelation => $relationData )
        {
            if( $relationType != 'MorphTo' )
            {
                $classUrl = property_exists( $relationData, 'related' )? $relationData->related : $this->configurationOptions[ 'model' ][ 'namespace' ] . '\\' . $modelRelation;
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

    public function setFileName() : void
    {
        $this->fileName = date( 'Y_m_d_His' ) . '_' . Str::snake( $this->classname ) . '.php';
    }

    public function generateFileContent() : void
    {
        $this->fileContent = File::get( __DIR__ . '/../Stubs/migration.stub' );
        $this->fileContent = str_replace( "\\t", "\t", $this->fileContent );
        $this->fileContent = str_replace( "\\n", "\n", $this->fileContent );
        $this->fileContent = str_replace( '{{ namespace }}', $this->classNamespace, $this->fileContent );
        $this->fileContent = str_replace( '{{ use }}', empty( $this->fileUseUrls )? '' : 'use ' . implode( ";\nuse ", $this->fileUseUrls ) . ";\n\n", $this->fileContent );
        $this->fileContent = str_replace( '{{ classname }}', $this->classname, $this->fileContent );
        $this->fileContent = str_replace( '{{ extends }}', $this->fileExtends, $this->fileContent );
        $this->fileContent = str_replace( '{{ interfaces }}', $this->fileInterfaces, $this->fileContent );
        $this->fileContent = str_replace( '{{ traits }}', $this->fileTraits, $this->fileContent );
        $this->fileContent = str_replace( '{{ table }}', $this->table, $this->fileContent );
        $this->fileContent = str_replace( '{{ columns }}', $this->columns, $this->fileContent );
        $this->fileContent = str_replace( '{{ relations }}', $this->relations, $this->fileContent );
    }
}
