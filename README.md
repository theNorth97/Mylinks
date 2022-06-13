<!-- <h2 align="center">Всем привет, меня зовут <a href="https://github.com/yulkabal" target="_blank">Юлия</a>
<img src="https://github.com/blackcater/blackcater/raw/main/images/Hi.gif" height="32"/></h2>   -->
 <h1 align="center">Сервис онлайн библиотека</h1>
  <p>Этот проект реализован с помощью PHP 8.1 , фреймворка Slim 4, doctrine, PostgreSql и Nginx.

  
<h2>API:</h2>
<ul>
  
- POST/signUp - регистрация и выдача токена в заголовке Token
  
- POST/signIn - аутентификация и выдача токена в заголовке Token

- POST/books - создать книгу
  
- GET/books/{title} - получить одну книгу
  
- GET/books - получить все книги
  
- PUT/books/{id} - обновить книгу
  
- DELETE/books/{id} - удалить книгу

- POST/userBooks - бронирование книги, но если книга занята, то добавляем читателя в лист ожидания
  
- DELETE/userBooks/{bookId} - возврат книги
</ul>

<h2>Чтобы запустить проект, выполните:</h2>

 1. Создайте контейнеры:

```docker-compose build```

2. Запустите их:

```docker-compose up -d```

3. Проверьте созданные docker-контейнеры:

```docker ps```

4. Готово 😊
