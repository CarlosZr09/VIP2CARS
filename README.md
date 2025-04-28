# VIP2CARS

Proyecto de prueba para el proceso de selección.

## Tecnologías

- **Laravel** 12  
- **PHP** 8.2  
- MySQL 8 (o superior)  
- Composer  

## Instalación

1. Clonar repo  
   `git clone https://github.com/CarlosZr09/VIP2CARS`  
2. Instalar dependencias  
   `composer install`  
3. Configurar `.env` 
   `copiar archivo env.example y ponerle como nombre .env` 
4. Migrar base de datos  
   `php artisan migrate`  
5. Ejecutar Seeder para documentos  
   `php artisan make:seeder DocumentSeeder`  
6. Levantar servidor  
   `php artisan serve`

## Uso

- Acceder a `/vehicles` para gestionar vehículos (Se puede crear clientes en la misma vista).
- Acceder a `/customers` para gestionar clientes.

## Rutas

| Método | Ruta                   | Descripción                                                                          |
|--------|-------------------------|--------------------------------------------------------------------------------------|
| GET    | /customers               | Vista clientes                                                                      |
| GET    | /customers/json          | JSON clientes                                                                       |
| POST   | /customer/create         | Crear cliente                                                                       |
| PUT    | /customer/update/{id}    | Actualizar cliente                                                                  |
| GET    | /customer/search/        | Mostrar cliente en base al tipo de documento y número de documento (QueryString)    |
| GET    | /customer/data/{id}      | Mostrar cliente específico en base al ID                                            |
| DELETE | /customer/destroy/{id}   | Eliminar cliente y sus vehículos                                                    |
| GET    | /vehicles                | Vista vehículos                                                                     |
| GET    | /vehicles/json           | JSON vehículos                                                                      |
| POST   | /vehicles/create         | Crear vehículo                                                                      |
| PUT    | /vehicles/update/{id}    | Actualizar vehículo                                                                 |
| GET    | /vehicles/data/{id}      | Mostrar vehículo específico en base al ID                                           |
| DELETE | /vehicles/destroy/{id}   | Eliminar vehículo                                                                   |

## Funcionalidades adicionales

- **Autocompletado inteligente de usuarios**:  
  Al crear un nuevo vehículo, el sistema permite seleccionar el tipo de documento e ingresar el número.  
  Automáticamente busca en la base de datos si el usuario ya existe y, de ser así, autocompleta los campos del formulario, optimizando el flujo de registro y mejorando la experiencia de usuario.
