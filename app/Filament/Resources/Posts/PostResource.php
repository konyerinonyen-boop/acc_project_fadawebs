<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\Pages\ViewPost;
use App\Filament\Resources\Posts\Schemas\PostForm;
use App\Filament\Resources\Posts\Schemas\PostInfolist;
use App\Filament\Resources\Posts\Tables\PostsTable;
use App\Models\Post;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use illuminate\Database\Eloquent\Builder;
class PostResource extends Resource
{
    public static function getEloquentQuery(): Builder
{
    $user = auth()->user();

    $query = parent::getEloquentQuery();

    // Super admin sees all posts
    if ($user->hasRole('super_admin')) {
        return $query;
    }

    // Zonal admin sees only posts in their zone
    return $query->where('zone_id', $user->zone_id);
}
    protected static ?string $model = Post::class;

    protected static string|\UnitEnum|null $navigationGroup = 'post Management';

     protected static ?string $navigationLabel = ' Manage posts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PlusCircle;

    protected static ?string $recordTitleAttribute = 'Post';

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PostInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'view' => ViewPost::route('/{record}'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
