# PowerGrid Common Components

This directory contains reusable components specifically designed for PowerGrid tables.

## Components

### `table-header.blade.php`

A reusable header component for PowerGrid tables that includes:

-   Dynamic entity title (e.g., "Users Management", "Products Management")
-   Add button with entity-specific method call
-   Export button with entity-specific method call

## Usage

### In your PowerGrid Table Component:

```php
<?php

namespace App\Livewire\YourEntity;

use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Header;

final class Table extends PowerGridComponent
{
    public string $tableName = 'your_entities';

    // Entity configuration
    public string $entityName = 'YourEntity';
    public string $entityNamePlural = 'YourEntities';
    public string $createRoute = 'your-entities.create';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            (new Header())
                ->showSearchInput()
                ->showToggleColumns()
                ->includeViewOnTop('components.powergrid.table-header', [
                    'addMethod' => 'addYourEntity',
                    'exportMethod' => 'exportYourEntities',
                ]),
            // ... rest of your setup
        ];
    }

    // Required methods
    public function addYourEntity()
    {
        $this->redirect(route($this->createRoute));
    }

    public function exportYourEntities()
    {
        session()->flash('success', 'Export functionality will be implemented soon.');
    }
}
```

### Required Properties:

-   `$entityName`: Single entity name (e.g., "User", "Product")
-   `$entityNamePlural`: Plural entity name (e.g., "Users", "Products")
-   `$createRoute`: Route name for creating new entity

### Required Methods:

-   `add{EntityName}()`: Method to handle add button click
-   `export{EntityNamePlural}()`: Method to handle export button click

## Examples

### User Management:

```php
public string $entityName = 'User';
public string $entityNamePlural = 'Users';
public string $createRoute = 'users.create';

// Methods:
public function addUser() { /* ... */ }
public function exportUsers() { /* ... */ }
```

### Product Management:

```php
public string $entityName = 'Product';
public string $entityNamePlural = 'Products';
public string $createRoute = 'products.create';

// Methods:
public function addProduct() { /* ... */ }
public function exportProducts() { /* ... */ }
```

## Benefits

-   **Consistent UI**: Same header design across all PowerGrid tables
-   **Easy to Use**: Just set properties and methods
-   **Maintainable**: Changes in one place affect all tables
-   **Reusable**: Works with any entity type
