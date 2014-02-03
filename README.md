#Statamic-JSON

Inspired by the Ruby on Rails Jbuilder gem https://github.com/rails/jbuilder

JSON for Statamic gives you a simple DSL for declaring JSON structures in Statamic templates. You can use Statamic conditionals, loops, and entries within the JSON structure to control output.

---

## DSL

- `json:pretty` Outputs pretty JSON
- `json:ugly` Outputs minified JSON
- `json(name,value)` Creates a JSON named value
- `json:hash[0-9]` Creates a JSON hash
- `json:hash[0-9](name)` Creates a JSON named hash
- `json:array[0-9]` Creates a JSON array
- `json:array[0-9](name)` Creates a JSON named array

---

## Usage

#### Template
```
{{ json:ugly }}
  {{ json:hash1 }}
    {{ json name="first_name" value="Brant" }}
    {{ json name="last_name" value="Wedel" }}
  {{ /json:hash1 }}
{{ /json:ugly }}
```
#### Output
`{"first_name":"Brant","last_name":"Wedel"}`

---

## Example

Note the indexed `json:array0` and `json:array1` for proper nesting. Also `json:pretty` will strip any trailing `,` from the end of hashes or arrays.

```
  <script>
  var resources = {{ json:pretty }}
    {{ json:array0 }}
      {{ entries:listing folder="gallery" }}
        {{ if photos }}
          {{ json:hash1 }}
            {{ json name="title" value="{title}" }}
            {{ json name="slug" value="{slug}" }}
            {{ json:array1 name="thumbnails" }}
              {{ photos }}
                {{ if index == 1 || featured }}
                "{{ transform src="{img}" width="200" }}",
                {{ endif }}
              {{ /photos }}
            {{ /json:array1 }}
          {{ /json:hash1 }}
        {{ endif }}
      {{ /entries:listing }}
    {{ /json:array0 }}
  {{ /json:pretty }};
  </script>
```

### Output
```
  <script>
  var photo_gallery = [
    {
      "title": "Vacation Photos",
      "slug": "vacation-photos",
      "thumbnails": [
        "/assets/img/gallery/picture-of-dog-r-w200-q75-m1391402149.jpg",
        "/assets/img/gallery/sightseeing-adventure-r-w200-q75-m1391402333.jpg"
      ]
    },
    {
      "title": "Work Photos",
      "slug": "work-photos",
      "pthumbnailshotos": [
        "/assets/img/gallery/coffee-cup-r-w200-q75-m1391402149.jpg",
        "/assets/img/gallery/recent-project-r-w200-q75-m1391402333.jpg",
        "/assets/img/gallery/work-vehicle-r-w200-q75-m1391402333.jpg"
      ]
    }
  ];
  </script>
```
