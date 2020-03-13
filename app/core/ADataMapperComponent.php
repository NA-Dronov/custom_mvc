<?php

class ADataMapperComponent extends Component
{
    protected $dataComponent = null;

    public function __construct(IDataComponent $dataComponent)
    {
        $this->dataComponent = $dataComponent;
    }

    protected function query(string $query, array $params = [])
    {
        $statement = $this->dataComponent->getConnection()->prepare($query);
        if (!empty($params)) {
            foreach ($params as $placeholder => $data) {
                $statement->bindValue($placeholder, $data['value'], !empty($data['type']) ? $data['type'] : PDO::PARAM_STR);
            }
        }

        $statement->execute();
        return $statement;
    }

    protected function row(string $query, array $params = []): ?array
    {
        $result = $this->query($query, $params)->fetch(PDO::FETCH_ASSOC);

        return $result === false ? null : $result;
    }

    protected function rows(string $query, array $params = []): ?array
    {
        $result = $this->query($query, $params)->fetchAll(PDO::FETCH_ASSOC);

        return $result === false ? null : $result;
    }

    protected function field(string $query, array $params = [])
    {
        $result = $this->query($query, $params)->fetchColumn();

        return $result === false ? null : $result;
    }
}
