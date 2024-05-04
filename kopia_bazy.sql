--
-- PostgreSQL database dump
--

-- Dumped from database version 15.5
-- Dumped by pg_dump version 15.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: update_active_status(); Type: FUNCTION; Schema: public; Owner: app
--

CREATE FUNCTION public.update_active_status() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.warranty_end_date := NEW.purchase_date + INTERVAL '1 year' * NEW.warranty_period;

    IF NEW.warranty_end_date >= CURRENT_DATE THEN
        NEW.active := true;
    ELSE
        NEW.active := false;
    END IF;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_active_status() OWNER TO app;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO app;

--
-- Name: tag; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.tag (
    id integer NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.tag OWNER TO app;

--
-- Name: tag_id_seq; Type: SEQUENCE; Schema: public; Owner: app
--

CREATE SEQUENCE public.tag_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tag_id_seq OWNER TO app;

--
-- Name: user; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    email character varying(180) NOT NULL,
    roles json NOT NULL,
    password character varying(255) NOT NULL,
    id_user_details_id integer
);


ALTER TABLE public."user" OWNER TO app;

--
-- Name: user_details; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.user_details (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    surname character varying(100) NOT NULL
);


ALTER TABLE public.user_details OWNER TO app;

--
-- Name: user_details_id_seq; Type: SEQUENCE; Schema: public; Owner: app
--

CREATE SEQUENCE public.user_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_details_id_seq OWNER TO app;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: app
--

CREATE SEQUENCE public.user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_id_seq OWNER TO app;

--
-- Name: warranty; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.warranty (
    id integer NOT NULL,
    category character varying(100) NOT NULL,
    product_name character varying(100) NOT NULL,
    purchase_date date NOT NULL,
    warranty_period integer NOT NULL,
    id_user integer,
    receipt character varying(100) DEFAULT NULL::character varying NOT NULL,
    warranty_end_date date,
    active boolean NOT NULL
);


ALTER TABLE public.warranty OWNER TO app;

--
-- Name: warranty_id_seq; Type: SEQUENCE; Schema: public; Owner: app
--

CREATE SEQUENCE public.warranty_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.warranty_id_seq OWNER TO app;

--
-- Name: warranty_tag; Type: TABLE; Schema: public; Owner: app
--

CREATE TABLE public.warranty_tag (
    warranty_id integer NOT NULL,
    tag_id integer NOT NULL
);


ALTER TABLE public.warranty_tag OWNER TO app;

--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: tag tag_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.tag
    ADD CONSTRAINT tag_pkey PRIMARY KEY (id);


--
-- Name: user_details user_details_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.user_details
    ADD CONSTRAINT user_details_pkey PRIMARY KEY (id);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: warranty warranty_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.warranty
    ADD CONSTRAINT warranty_pkey PRIMARY KEY (id);


--
-- Name: warranty_tag warranty_tag_pkey; Type: CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.warranty_tag
    ADD CONSTRAINT warranty_tag_pkey PRIMARY KEY (warranty_id, tag_id);


--
-- Name: idx_5aab728b2ec1782c; Type: INDEX; Schema: public; Owner: app
--

CREATE INDEX idx_5aab728b2ec1782c ON public.warranty_tag USING btree (warranty_id);


--
-- Name: idx_5aab728bbad26311; Type: INDEX; Schema: public; Owner: app
--

CREATE INDEX idx_5aab728bbad26311 ON public.warranty_tag USING btree (tag_id);


--
-- Name: idx_88d71cf26b3ca4b; Type: INDEX; Schema: public; Owner: app
--

CREATE INDEX idx_88d71cf26b3ca4b ON public.warranty USING btree (id_user);


--
-- Name: uniq_8d93d649e7927c74; Type: INDEX; Schema: public; Owner: app
--

CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON public."user" USING btree (email);


--
-- Name: uniq_8d93d649f5230246; Type: INDEX; Schema: public; Owner: app
--

CREATE UNIQUE INDEX uniq_8d93d649f5230246 ON public."user" USING btree (id_user_details_id);


--
-- Name: warranty update_active_trigger; Type: TRIGGER; Schema: public; Owner: app
--

CREATE TRIGGER update_active_trigger BEFORE INSERT OR UPDATE ON public.warranty FOR EACH ROW EXECUTE FUNCTION public.update_active_status();


--
-- Name: warranty_tag fk_5aab728b2ec1782c; Type: FK CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.warranty_tag
    ADD CONSTRAINT fk_5aab728b2ec1782c FOREIGN KEY (warranty_id) REFERENCES public.warranty(id) ON DELETE CASCADE;


--
-- Name: warranty_tag fk_5aab728bbad26311; Type: FK CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.warranty_tag
    ADD CONSTRAINT fk_5aab728bbad26311 FOREIGN KEY (tag_id) REFERENCES public.tag(id) ON DELETE CASCADE;


--
-- Name: warranty fk_88d71cf26b3ca4b; Type: FK CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public.warranty
    ADD CONSTRAINT fk_88d71cf26b3ca4b FOREIGN KEY (id_user) REFERENCES public."user"(id) ON DELETE CASCADE;


--
-- Name: user fk_8d93d649f5230246; Type: FK CONSTRAINT; Schema: public; Owner: app
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT fk_8d93d649f5230246 FOREIGN KEY (id_user_details_id) REFERENCES public.user_details(id);


--
-- PostgreSQL database dump complete
--