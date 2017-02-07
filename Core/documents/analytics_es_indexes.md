# Configuring Elasticsearch for Analytics

## Ping Index template

```
curl -XPUT 'http://localhost:9200/_template/expansion_ping_1?pretty' -H 'Content-Type: application/json' -d'
{
  "order": 0,
  "template": "expansion-ping-*",
  "settings": {
    "index.number_of_replicas": "1",
    "index.number_of_shards": "1"
  },
  "mappings": {
    "servers": {
      "properties": {
        "plugins": {
          "type": "string",
          "fields": {
            "raw": {
              "type": "keyword",
              "index": "not_analyzed"
            }
          }
        },
        "version": {
            "type": "keyword"
        },
        "country": {
            "type": "keyword"
        },
        "game": {
            "type": "keyword"
        },
        "php_version": {
            "type": "keyword"
        },
        "php_version_short": {
            "type": "keyword"
        },
        "mysql_version": {
            "type": "keyword"
        },
        "title": {
            "type": "keyword"
        },
        "mode": {
            "type": "keyword"
        },
        "serverOs": {
            "type": "keyword"
        }
      }
    }
  },
  "aliases": {}
}
'
```

## Error Index template

```
curl -XPUT 'http://localhost:9200/_template/expansion_error_1?pretty' -H 'Content-Type: application/json' -d'
{
  "order": 0,
  "template": "expansion-error-*",
  "settings": {
    "index.number_of_replicas": "1",
    "index.number_of_shards": "1"
  },
  "mappings": {
    "servers": {
      "properties": {
        "plugins": {
          "type": "string",
          "fields": {
            "raw": {
              "type": "keyword",
              "index": "not_analyzed"
            }
          }
        },
        "version": {
            "type": "keyword"
        },
        "country": {
            "type": "keyword"
        },
        "game": {
            "type": "keyword"
        },
        "php_version": {
            "type": "keyword"
        },
        "php_version_short": {
            "type": "keyword"
        },
        "mysql_version": {
            "type": "keyword"
        },
        "title": {
            "type": "keyword"
        },
        "mode": {
            "type": "keyword"
        },
        "serverOs": {
            "type": "keyword"
        }
      }
    }
  },
  "aliases": {}
}
'
```


## Profiling Index template

```
curl -XPUT 'http://localhost:9200/_template/expansion_profiling_1?pretty' -H 'Content-Type: application/json' -d'
{
  "order": 0,
  "template": "expansion-profiling-*",
  "settings": {
    "index.number_of_replicas": "1",
    "index.number_of_shards": "1"
  },
  "mappings": {
    "servers": {
      "properties": {
        "plugins": {
          "type": "string",
          "fields": {
            "raw": {
              "type": "keyword",
              "index": "not_analyzed"
            }
          }
        },
        "version": {
            "type": "keyword"
        },
        "country": {
            "type": "keyword"
        },
        "game": {
            "type": "keyword"
        },
        "php_version": {
            "type": "keyword"
        },
        "php_version_short": {
            "type": "keyword"
        },
        "mysql_version": {
            "type": "keyword"
        },
        "title": {
            "type": "keyword"
        },
        "mode": {
            "type": "keyword"
        },
        "serverOs": {
            "type": "keyword"
        }
        "task": {
            "type": "keyword"
        }
      }
    }
  },
  "aliases": {}
}
'
```