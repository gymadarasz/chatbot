<?php declare(strict_types = 1);

namespace GyMadarasz\ChatBot\Service;

use GyMadarasz\WebApp\Service\Mysql;

class Chats
{
    private Mysql $mysql;

    public function __construct(Mysql $mysql)
    {
        $this->mysql = $mysql;
    }

    public function create(string $name): bool
    {
        $_name = $this->mysql->escape($name);
        return (bool)$this->mysql->insert("INSERT INTO chat (name) VALUES ('$_name');");
    }

    public function modify(int $id, string $name): bool
    {
        $_name = $this->mysql->escape($name);
        return (bool)$this->mysql->update("UPDATE chat SET name = '$_name' WHERE id = $id AND deleted = 0 LIMIT 1;");
    }

    /** @return array<string> */
    public function retrieve(int $id): array
    {
        return (array)$this->mysql->selectOne("SELECT id, name FROM chat WHERE id = $id AND deleted = 0 LIMIT 1;");
    }

    /** @return array<array<string>> */
    public function getList(): array
    {
        return $this->mysql->select("SELECT id, name FROM chat WHERE deleted = 0;");
    }

    public function delete(int $id): bool
    {
        return (bool)$this->mysql->update("UPDATE chat SET deleted = 1 WHERE id = $id AND deleted = 0 LIMIT 1;");
    }
}
