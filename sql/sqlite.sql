CREATE TABLE "pages" (
  "id"      INTEGER PRIMARY KEY AUTOINCREMENT,
  "title"   TEXT UNIQUE NOT NULL,
  "content" TEXT
);
INSERT INTO "pages" VALUES (1, 'Index', 'Welcome to Kolibri.');


CREATE INDEX "title" ON "pages" ("title");

CREATE TABLE "versions" (
  "id"      INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
  "page_id" INTEGER                           NOT NULL REFERENCES "pages"("id"),
  "version" INTEGER                           NOT NULL,
  "content" TEXT                              NOT NULL,
  "created" DATETIME
);
INSERT INTO "versions" VALUES (1, 1, 1, 'Welcome to Kolibri.', '2013-05-12 16:17:57');


CREATE INDEX "page_id" ON "versions" ("page_id");
CREATE INDEX "version" ON "versions" ("version");