<?php

namespace TOTS\LaravelCrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use TOTS\LaravelCrudGenerator\FileGenerator;
use Illuminate\Support\Str;
use \stdClass;

class MigrationGenerator extends FileGenerator
{
    protected string $table = '';
    protected string $columns = '';
    protected string $constraints = '';
    protected array $morphs = [];
    protected array $manyToMany = [];

    public function setFileContent() : void
    {
        $this->setTable();
        $this->setRelationsAndConstraints();
        $this->setColumns();
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
        $this->columns .= $this->setMorphsColumns();
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

    public function setMorphsColumns() : string
    {
        $morphColumns = '';
        foreach( $this->morphs as $column => $columnData )
        {
            $type = $columnData? 'nullableMorphs' : 'morphs';
            $morphColumns .= "\t\t\t\$table->$type( '$column' );\n";
        }
        return $morphColumns;
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

    public function setRelationsAndConstraints() : void
    {
        if( $this->entityData && property_exists( $this->entityData, 'relations' ) && !empty( $this->entityData->relations ) )
        {
            foreach( $this->entityData->relations as $relationType => $relations )
            {
                $relationType = Str::studly( $relationType );
                if( $relations != new stdClass() )
                {
                    if( $relationType == 'BelongsTo' )
                    {
                        $this->constraints .= $this->setBelongsToConstraints( $relationType, $relations );
                    }
                    if( $relationType == 'BelongsToMany' )
                    {
                        $this->manyToMany = (array) $relations;
                        // $this->constraints .= $this->setBelongsToManyConstraints( $relationType, $relations );
                    }
                    if( $relationType == 'MorphTo' )
                    {
                        foreach( $relations as $relationName => $relationData )
                        {
                            $this->morphs[ Str::snake( $relationName ) ] = [
                                'nullable' => $relationData->nullable ?? true,
                            ];
                        }
                    }

                }
            }
        }
    }

    public function setBelongsToConstraints( string $relationType, object $relations ) : string
    {
        $constraints = "\n\t\t\t// $relationType\n";
        foreach( $relations as $modelRelation => $relationData )
        {
            $column = $relationData->foreingKey ?? Str::snake( $modelRelation ) . '_id';
            $table = Str::snake( Str::plural( $modelRelation ) );
            $reference = $relationData->localKey ?? $this->fileData->primaryKey ?? 'id';
            $extra = "->references( '$reference' )->on( '$table' )";
            $templateData = [
                'type' => 'foreign',
                'column' => $column,
                'extra' => $extra,
            ];
            $constraints .= parent::generateFromTemplate( 'constraint', $templateData );
        }
        return $constraints;
    }

    public function generateManyToManyMigrations() : void
    {
        foreach( $this->manyToMany as $modelRelation => $relationData )
        {
            $table = $this->getRelationTableName( $this->entityName, $modelRelation );
            $classname = 'Create' . Str::studly( $table ) . 'Table';
            $entity1 = $relationData->foreingPivotKey ??
                $this->entityData->model->primary_key ?? Str::snake( Str::singular( $this->entityName ) ) . '_id';
            $entity2 = $relationData->relatedPivotKey ?? Str::snake( Str::singular( $modelRelation ) ) . '_id';
            $objectData = new stdClass();
            $objectData->nameSingular = $table;
            $objectData->namePlural = $table;
            $objectData->files = [ 'migration' ];
            $objectData->attributes = [
                $entity1 => [
                    'type' => 'bigInteger',
                    'nullable' => false,
                ],
                $entity2 => [
                    'type' => 'bigInteger',
                    'nullable' => false,
                ]
            ];
            $objectData->migration = [
                'table' => $table,
                'classname' => $classname,
                'rewrite' => $this->fileCanBeRewrited()
            ];
            $generator = new MigrationGenerator( Str::studly( $table ), json_decode( json_encode( $objectData ) ) );
            $generator->createFile();
        }
    }

    public function getMorphToConstraintData( string $modelRelation, object $relationData ) : array
    {
        $column = Str::snake( $modelRelation );
        return [
            'type' => 'morphs',
            'column' => $column,
            'extra' => '',
        ];
    }

    public function getRelationTableName( $entity1, $entity2 )
    {
        $entities = [ $entity1, $entity2 ];
        sort( $entities );
        return Str::singular( Str::snake( $entities[ 0 ] ) ) . "_" . Str::singular( Str::snake( $entities[ 1 ] ) );
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
        $this->fileContent = str_replace( '{{ constraints }}', $this->constraints, $this->fileContent );
        $this->generateManyToManyMigrations();
    }
}
